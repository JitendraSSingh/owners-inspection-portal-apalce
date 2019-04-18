<?php

namespace App\Controllers\Admin\Inspection;

use Slim\Views\Twig;

use Psr\Http\Message\ResponseInterface as Response;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Support\FileHandling\Contracts\FileHandlingInterface;

use App\Support\Purifier\Purify;

use App\Models\Property;

use App\Models\Inspection;

use App\Models\File;

use Slim\Interfaces\RouterInterface as Router;

use App\Helpers\AllowedFileMeta;

use App\Helpers\Uploader;

class InspectionController{

	private $uploader;

	private $purifier;

	private $fileHandling;

	protected $router;

	public function __construct(Purify $purify ,Uploader $uploader ,Router $router, FileHandlingInterface $fileHandling){

		$this->fileHandling = $fileHandling;

		$this->purify = $purify;

		$this->uploader = $uploader;

		$this->router = $router;
	}

	public function index( Response $response, Twig $view){


		return $view->render($response, 'admin/inspection/index.twig');

	}

	public function getAddInspection(Request $request, Response $response, Twig $view){


		$propertyid = $request->getAttribute('route')->getArgument('propertyid');

		if($propertyid){
		
			$property = Property::where('id', $propertyid)->first();
		}
		return $view->render($response, 'admin/inspection/add.twig', compact('property','referer'));

	}


	public function postAddInspection(Request $request, Response $response, Twig $view){

		$property = Property::where('code', $request->getParam('property_code'))->first();

		if($property){

			//First Insert Inspection
			$timestamp = strtotime($request->getParam('datetimepicker'));

			$datetime = date('Y-m-d H:i:s', $timestamp);

			$short_statement = $request->getParam('short_statement');

			$short_statement = $this->purify->makePure($short_statement);

			$description = $request->getParam('description');

			$description = $this->purify->makePure($description);

			$description = htmlspecialchars($description);

			$urls = [];

			foreach ($request->getParam('urls') as $key => $value) {

				$urls[] = $this->purify->makePure($value);

			}

			try{

				$inspection = Inspection::create([

					'property_id' => $property->id ,

					'date' => $datetime ,

					'short_statement' => $short_statement ,

					'description' => $description ,

					'urls' => json_encode($urls)

				]);
			}

			catch(Exception $e){
                return $response->withRedirect($this->router->pathFor('admin.search.property.view.propertyid',[
				'propertyid' => $property->id
			    ]));
			}

		}

		$uploadedFiles = $request->getUploadedFiles();

		$imageCaptions = $request->getParam('image_file_caption');

		$fileCaptions = $request->getParam('attachment_file_caption');		

		if($uploadedFiles['inspection_images']){

			$storageDirectory = '/var/www/html/public/inspectionimages';

			$uploadedImageFiles = $this->uploader->setStorageDirectory($storageDirectory)->uploadImageFiles($uploadedFiles['inspection_images']);


			if(!empty($uploadedImageFiles)){

				$imageCaptionsAvailable = (!empty($imageCaptions)) ? $imageCaptions : FALSE;
				
				foreach ($uploadedImageFiles as $key => $file){
					File::create([
						'inspection_id' => $inspection->id,
						'owner_code' => $property->owner_code,
						'name' => $file['real_name'],
						'caption' => $imageCaptionsAvailable ? $imageCaptionsAvailable[$key] : NULL,
						'random_name' => $file['random_image_filename'],
						'location' => $storageDirectory,
						'type' => 'image_attachment',
						'mime_type' => $file['type']
					]);
				}
			}
		}

		if($uploadedFiles['attachments']){

			$storageDirectory = '/var/www/html/inspectionfiles';

			$uploadedInspectionFiles = $this->uploader->setStorageDirectory($storageDirectory)->uploadAttachmentFiles($uploadedFiles['attachments']);

			if(!empty($uploadedInspectionFiles)){

				$fileCaptionsAvailable = (!empty($fileCaptions)) ? $fileCaptions : FALSE;

				foreach ($uploadedInspectionFiles as $key => $file){
					File::create([
						'inspection_id' => $inspection->id,
						'owner_code' => $property->owner_code,
						'name' => $file['real_name'],
						'caption' => $fileCaptionsAvailable ? $fileCaptionsAvailable[$key] : NULL,
						'random_name' => $file['random_filename'],
						'location' => $storageDirectory,
						'type' => 'file_attachment',
						'mime_type' => $file['type']
					]);
				}
			}
		}



		 return $response->withRedirect($this->router->pathFor('admin.search.property.view.propertyid',[
				'propertyid' => $property->id
			    ]));

	}


	public function getViewInspection($id, $propertyid, Response $response, Twig $view){

		$inspection = Inspection::where('id',$id)->where('property_id',$propertyid)->first();

		$property = Property::find($propertyid);

		return $view->render($response, 'admin/inspection/view.twig', compact('inspection','property'));

	}

	public function getFile($filename, Response $response){
		$this->fileHandling->load($filename ,$response);
	}

	public function postDeleteInspection($id,$propertyid,Request $request, Response $response, Twig $view){

		$inspection = Inspection::where('id', $id)->where('property_id', $propertyid)->delete();


		return $response->withRedirect($this->router->pathFor('admin.search.property.view.propertyid', ['propertyid' => $propertyid]));

	}

	public function getEditInspection($id, $propertyid, Response $response, Twig $view){

			$property = Property::find($propertyid);

			$inspection = Inspection::where('id',$id)->where('property_id',$propertyid)->first();

			if($inspection){

				$files = $inspection->files();

				return $view->render($response, 'admin/inspection/edit.twig', compact('inspection','property'));
			}

			return $response->withRedirect($this->router->pathFor('admin.search.property.view.propertyid',[
				'propertyid' => $propertyid
			]));
	}

	public function postEditInspection($id, $propertyid, Request $request, Response $response){

		$property = Property::where('code', $request->getParam('property_code'))->first();

		if($property){

			//First Update Inspection
			$timestamp = strtotime($request->getParam('datetimepicker'));

			$datetime = date('Y-m-d H:i:s', $timestamp);

			$short_statement = $request->getParam('short_statement');

			$short_statement = $this->purify->makePure($short_statement);

			$description = $request->getParam('description');

			$description = $this->purify->makePure($description);

			$description = htmlspecialchars($description);

			$urls = [];

			if($request->getParam('urls')){

				foreach ($request->getParam('urls') as $key => $value) {

					$urls[] = $this->purify->makePure($value);

				}
			}

			try{

					$inspection = Inspection::where('id',$id)->first();

					$inspection->property_id = $property->id;

					$inspection->date = $datetime ;

					$inspection->short_statement = $short_statement ;

					$inspection->description = $description ;

					$inspection->urls = json_encode($urls);

					$inspection->save();

			}



			catch(Exception $e){
				return $response->withRedirect($this->router->pathFor('admin.inspections.add'));
			}

			

		if($inspection){

			$edit_image_captions = $request->getParam('edit_image_file_caption');

			$edit_file_captions = $request->getParam('edit_attachment_file_caption');

			if($edit_image_captions){

			    foreach ($inspection->imageFiles() as $key => $file) {
				
				   $file->caption = $edit_image_captions[$key];

				   $file->save();
			    
			    }

			}

			if($edit_file_captions){

			    foreach ($inspection->attachmentFiles() as $key => $file) {
				
				   $file->caption = $edit_file_captions[$key];
			    
			       $file->save();
			    }

			}			
			

			if($request->getParam('delete_images')){

					foreach ($request->getParam('delete_images') as $key => $value) {

						File::where('id',$value)->delete();

					}

			}
				if($request->getParam('delete_attachfiles')){

					foreach ($request->getParam('delete_attachfiles') as $key => $value) {

						File::where('id',$value)->delete();

					}

				}
			}

				$uploadedFiles = $request->getUploadedFiles();
				
				$imageCaptions = $request->getParam('image_file_caption');

				$fileCaptions = $request->getParam('attachment_file_caption');	

				if($uploadedFiles['inspection_images']){

					$storageDirectory = '/var/www/html/public/inspectionimages';

					$uploadedImageFiles = $this->uploader->setStorageDirectory($storageDirectory)->uploadImageFiles($uploadedFiles['inspection_images']);

					if(!empty($uploadedImageFiles)){

						$imageCaptionsAvailable = (!empty($imageCaptions)) ? $imageCaptions : FALSE;

						foreach ($uploadedImageFiles as $key => $file){
							File::create([
								'inspection_id' => $inspection->id,
								'owner_code' => $property->owner_code,
								'name' => $file['real_name'],
								'caption' => $imageCaptionsAvailable ? $imageCaptionsAvailable[$key] : NULL,
								'random_name' => $file['random_image_filename'],
								'location' => $storageDirectory,
								'type' => 'image_attachment',
								'mime_type' => $file['type']
							]);
						}
					}
				}

				if($uploadedFiles['attachments']){

					$storageDirectory = '/var/www/html/inspectionfiles';

					$uploadedInspectionFiles = $this->uploader->setStorageDirectory($storageDirectory)->uploadAttachmentFiles($uploadedFiles['attachments']);

					if(!empty($uploadedInspectionFiles)){

						$fileCaptionsAvailable = (!empty($fileCaptions)) ? $fileCaptions : FALSE;
						
						foreach ($uploadedInspectionFiles as $key => $file){
							File::create([
								'inspection_id' => $inspection->id,
								'owner_code' => $property->owner_code,
								'name' => $file['real_name'],
								'caption' => $fileCaptionsAvailable ? $fileCaptionsAvailable[$key] : NULL,
								'random_name' => $file['random_filename'],
								'location' => $storageDirectory,
								'type' => 'file_attachment',
								'mime_type' => $file['type']
							]);
						}
					}
				}

		}

		return $response->withRedirect($this->router->pathFor('admin.inspections.edit', ['id' => $id, 'propertyid' => $property->id]));

	}

}