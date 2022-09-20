<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\PhotographicPackages;
use App\Models\adminpanel\packages_categories;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class PackagesController extends Controller
{
    
    
    

    function __construct() {
        $this->packages= new PhotographicPackages;
        $this->package_category= new packages_categories;
      }
      // List All the packages 
    public function packages($type=NULL){
        $user=Auth::user();
        
            $packagesData=$this->packages->with('category')
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/packages',compact('packagesData','user'));
    }
      // List All the Categories 
    public function categoreis(){
        $user=Auth::user();
        
            $categoriesData=$this->package_category
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel/categories',compact('categoriesData','user'));
    }
      public function addpackages(){
        $user=Auth::user(); 
        
         return view('adminpanel/add_packages',compact('user'));
     }
    public function add_documents($id){
        $user=Auth::user(); 
        $userData=$this->users->where('id',$id)->with('files')->with('city')->with('ZipCode')->with('getGroups')->get()->toArray();
       
         return view('adminpanel/uploadform',compact('user','userData'));
         return view('adminpanel/add_package_documents',compact('user','userData'));
     }
    public function upload_documents($id,Request $request){
        $user=Auth::user();
            $image = $request->file('file');
            $imageExt=$image->extension();
            $imageName = time().'.'.$imageExt;

     

            $image->move(public_path('uploads'),$imageName);
            $orginalImageName=$image->getClientOriginalName();
        
        //return response()->json(['success'=>$imageName]);

            $this->files->name=$orginalImageName;
            $this->files->slug=phpslug($imageName);
            $this->files->path=url('uploads').'/'.$imageName;
            $this->files->description=$orginalImageName.' file uploaded';
            $this->files->otherinfo=$imageExt;
            $this->files->user_id=$id;
            $this->files->save();
        //             ->update($data);
        // $this->files->where('id', $id)
        //             ->update($data);

                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' uploaded documents for package';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'package_documents_added',
                        'comments'=>$activityComment,
                        'others'=>'files',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return response()->json(['success'=>$imageName]);

        
     }
     public function SavepackagesData(Request $request){
       
        $validator=$request->validate([
            'name'=>'required',
            'price'=>'required',
            'cat_id'=>'required',
        ]);
        
        
        $this->packages->name=$request['name'];
        $this->packages->price=$request['price'];
        $this->packages->description=($request['description']);
        $this->packages->is_active=1;
        $this->packages->user_id=get_session_value('id');
       

       
       
        if(isset($request['other_category']) && !empty($request['other_category']))
        $cat_id = getOtherCategory($request['other_category']);
        else
        $cat_id=$request['cat_id'];
        $this->packages->cat_id=$cat_id;

   
  
        $request->session()->flash('alert-success', 'package Added! Please Check in packages list Tab');
        $this->packages->save();
       
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new package '.$this->packages->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->packages->id,
                        'action_slug'=>'package_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    
    public function UpdateUsersData($id,Request $request)
    {
        $dataArray['error']='No';
        

        $validated =  $request->validate([
            'name' => 'required',
            'group_id' => 'required'
            ]);
            if(!$validated){

                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
                echo json_encode($dataArray);
                die;

            }
     
        $data['name']=$request['name'];
        $data['group_id']=$request['group_id'];

        $groupData=Groups::find($request['group_id']);
       
        $groupData=$groupData->toArray();
        // p($groupData);
        // die;
        $dataArray['name']=$data['name'];
        $dataArray['id']=$id;
        $dataArray['group_title']=$groupData['title'];
        $dataArray['group_role']=$groupData['role'];
        
        $dataArray['msg']='Mr.'.get_session_value('name').', '.$data['name'].' record Successfully Updated !';
        $this->users->where('id', $id)
                    ->update($data);

                    $activityComment='Mr.'.get_session_value('name').' updated User '.$data['name'].' Record';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'user_record_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);
        echo json_encode($dataArray);
        die;

    }
    public function DeleteLeadssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved lead to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_status_changed',
                'comments'=>$activityComment,
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function ajaxcall($id, Request $req){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action']=='deletepackage')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->packages->where('id','=',$id)->update(array('is_active'=>0));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'package_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted package',
                'others'=>'packages',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action']=='delete_category')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->package_category->where('id','=',$id)->update(array('is_active'=>0));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'category_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted category',
                'others'=>'categories',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='save_category_add_form'){
            $dataArray['error']='No';
            $dataArray['msg']='Category Successfully Added';
            $dataArray['title']='Category Panel';
            $dataArray['actionType']='categories_added';
            $dataArray['name']=$req['name'];


            $this->package_category->name=$req['name'];
            $this->package_category->slug=phpslug($req['name']);
            $this->package_category->user_id=get_session_value('id');

            $this->package_category->save();
             // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$this->package_category->id,
                'action_slug'=>'category_update',
                'comments'=>'Mr.'.get_session_value('name').' added a category '.$req['name'],
                'others'=>'categories',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action'] =='save_category_edit_form'){
            $dataArray['error']='No';
            $dataArray['msg']='Category Successfully Updated';
            $dataArray['title']='Category Panel';
            $dataArray['actionType']='categories_updated';

            $dataArray['name']=$req['name'];
            $dataArray['id']=$req['cat_id'];

            $this->package_category->where('id', $req['cat_id'])->update(
                array(
                    'name'=>$req['name'],
                    'slug'=>phpslug($req['name']),
                    'user_id'=>get_session_value('id'),
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['cat_id'],
                'action_slug'=>'category_update',
                'comments'=>'Mr.'.get_session_value('name').' updated a category '.$req['name'],
                'others'=>'package',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtopackageForm'){
            $dataArray['error']='No';
            $dataArray['msg']='package Successfully Updated';
            $dataArray['title']='package Panel';
            $dataArray['actionType']='package_update';
            //$dataArray['formdata']=$req->all();

            $dataArray['name']=$req['name'];
            $dataArray['price']=$req['price'];
            $dataArray['id']=$req['package_id'];
            $dataArray['description']=$req['description'];
            $dataArray['catname']=$req['catname'];
            
            if(isset($req['other_category']) && !empty($req['other_category']))
            $cat_id = getOtherCategory($req['other_category']);
            else
            $cat_id=$req['cat_id'];
            $this->packages->cat_id=$cat_id;


            $this->packages->where('id', $req['package_id'])->update(
                array(
                    'name'=>$req['name'],
                    'price'=>$req['price'],
                    'description'=>$req['description'],
                    'cat_id'=>$cat_id,
                    
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['package_id'],
                'action_slug'=>'package_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a package '.$req['name'],
                'others'=>'package',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;

        }
       
        else if(isset($req['action']) && $req['action'] =='editpackageForm'){
            $dataArray['error']='No';
           
           
            $data=$this->packages->with('category')
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditpackageForm"
                                                                            method="GET" action="" onsubmit="return updatepackage('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtopackageForm" />
                                                                            <input type="hidden" name="package_id" value="'.$data['id'].'" />
                                                                            <input type="hidden" id="catname" name="catname" value="'.$data['category']['name'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            value="'. $data['name'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="price"
                                                                                            class="form-control"
                                                                                            placeholder=" price"
                                                                                            value="'. $data['price'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <textarea type="text" name="description" class="form-control"
                                                                                            placeholder="Description"
                                                                                            >'. $data['description'].'</textarea>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                           
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="cat_id" onChange="changeProCategory()" name="cat_id" class="form-control select2bs4" placeholder="Select Category">'.getpackageCatOptions($data['cat_id']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="other_cat"></div>
                                                                            
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
        else if(isset($req['action']) && $req['action'] =='editCategoryForm'){
            $dataArray['error']='No';
           
           
            $data=$this->package_category
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="edit_categories_form"
                                                                            method="GET" action="" onsubmit="return update_category_form_data('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="save_category_edit_form" />
                                                                            <input type="hidden" name="cat_id" value="'.$data['id'].'" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            value="'. $data['name'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
       
      
        echo json_encode($dataArray);
        die;
    }
    public function categoryajaxcall(){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
            

            $dataArray['error']='No';
           
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="add_categories_form"
                                                                            method="GET" action="" onsubmit="return add_category_form_data()">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="save_category_add_form" />
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="name"
                                                                                            class="form-control"
                                                                                            placeholder=" Name"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
       
        echo json_encode($dataArray);
        die;
    }
 
}
