<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 2.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Accounting.php
 * @copyright : Reserved RamomCoders Team
 */

class Subject extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('subject_model');
    }

    public function index()
    {
        if (!get_permission('subject', 'is_view')) {
            access_denied();
        }
        $this->data['subjectlist'] = $this->app_lib->getTable('subject');
        $this->data['title'] = translate('subject');
        $this->data['sub_page'] = 'subject/index';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // subject edit page
    public function edit($id = '')
    {
        if (!get_permission('subject', 'is_edit')) {
            access_denied();
        }

        $this->data['subject'] = $this->app_lib->getTable('subject', array('t.id' => $id), true);
        $this->data['title'] = translate('subject');
        $this->data['sub_page'] = 'subject/edit';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // moderator subject all information
    public function save()
    {
        if ($_POST) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('name', translate('subject_name'), 'trim|required');
            $this->form_validation->set_rules('subject_code', translate('subject_code'), 'trim|required');
            $this->form_validation->set_rules('subject_type', translate('subject_type'), 'trim|required');
            if ($this->form_validation->run() !== false) {
                $arraySubject = array(
                    'name' => $this->input->post('name'),
                    'subject_code' => $this->input->post('subject_code'),
                    'subject_type' => $this->input->post('subject_type'),
                    'subject_author' => $this->input->post('subject_author'),
                    'branch_id' => $this->application_model->get_branch_id(),
                );
                $subjectID = $this->input->post('subject_id');
                if (empty($subjectID)) {
                    if (get_permission('subject', 'is_add')) {
                        $this->db->insert('subject', $arraySubject);
                    }
                    set_alert('success', translate('information_has_been_saved_successfully'));
                } else {
                    if (get_permission('subject', 'is_edit')) {
                        if (!is_superadmin_loggedin()) {
                            $this->db->where('branch_id', get_loggedin_branch_id());
                        }
                        $this->db->where('id', $subjectID);
                        $this->db->update('subject', $arraySubject);
                    }
                    set_alert('success', translate('information_has_been_updated_successfully'));
                }
                $url = base_url('subject');
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function delete($id = '')
    {
        if (get_permission('subject', 'is_delete')) {
            $this->app_lib->check_branch_restrictions('subject', $id);
            $this->db->where('id', $id);
            $this->db->delete('subject');
            $this->db->where('subject_id', $id);
            $this->db->delete('subject_assign');
        }
    }

    // add subject assign information and delete
    public function class_assign()
    {
        if (!get_permission('subject_class_assign', 'is_view')) {
            access_denied();
        }

        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['assignlist'] = $this->subject_model->getAssignList();
        $this->data['title'] = translate('class_assign');
        $this->data['sub_page'] = 'subject/class_assign';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }


	public function extra_assign_subject()
    {
        $assignlist = $this->subject_model->get_subject_extra_list();	
    	//print "<pre>"; print_r($assignlist);
        $count = 1;
    	$html ='';
        if (count($assignlist)){
           foreach ($assignlist as $row){
        	
           $html .='<tr>
                            <td>'.$count.'</td>
                        
                            <td>'.$row->branch_name.'</td>
							<td>'.$row->staff_name.'</td>
                            <td>'.$row->class_name.'</td>
                            <td>'.$row->section_name.'</td>
                            <td>'.$row->first_name.' '.$row->last_name.'</td>
                            <td class="text-dark">'.$row->name.'</td>
                            <td>
                              <a href="javascript:edit_extra_subject('.$row->id.','.$row->branch_id.','.$row->class_id.','.$row->section_id.','.$row->student_id.','.$row->teacher_id.','.$row->subject_id.');" class="btn btn-circle btn-default icon" >
                                    <i class="fas fa-pen-nib"></i>
                              </a>
                             <button class="btn btn-danger icon btn-circle" onclick="javascript:delete_extra_subject('.$row->id.');"><i class="fas fa-trash-alt"></i></button>   
                             </td>
                        </tr>';
       			 $count++;
        	}
        } else {
        	$html .='<tr><td colspan="7">Result not found.</td></tr>';
        }
    	print $html;
      
    }

 	public function extra_subject_delete($id = '')
    {
        
        $this->db->where('id', $id);
        $this->db->delete('subject_assign');
    }
    // moderator class assign save all information
    public function class_assign_save()
    {
        if ($_POST) {
            if (get_permission('subject_class_assign', 'is_add')) {
                if (is_superadmin_loggedin()) {
                    $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
                }
                $this->form_validation->set_rules('class_id', translate('class'), 'trim|required|callback_unique_subject_assign');
                $this->form_validation->set_rules('section_id', translate('section'), 'trim|required');
                $this->form_validation->set_rules('subjects[]', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $branchID = $this->application_model->get_branch_id();
                    $arraySubject = array(
                        'class_id' => $this->input->post('class_id'),
                        'section_id' => $this->input->post('section_id'),
                        'session_id' => get_session_id(),
                        'branch_id' => $branchID,
                    );

                    // get class teacher details
                    $get_teacher = $this->subject_model->get('teacher_allocation', $arraySubject, true);
                    $subjects = $this->input->post('subjects');
                    foreach ($subjects as $subject) {
                        $arraySubject['subject_id'] = $subject;
                        $query = $this->db->get_where("subject_assign", $arraySubject);
                        if ($query->num_rows() == 0) {
                            $arraySubject['teacher_id'] = empty($get_teacher) ? 0 : $get_teacher['teacher_id'];
                            $this->db->insert('subject_assign', $arraySubject);
                        }
                    }
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $url = base_url('subject/class_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }

	public function extra_subject_assign_save()
    {
    	
        if ($_POST) {
            if (get_permission('subject_class_assign', 'is_add')) {
                if (is_superadmin_loggedin()) {
                    $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
                }
                $this->form_validation->set_rules('class_id', translate('class'), 'trim|required');
                $this->form_validation->set_rules('section_id', translate('section'), 'trim|required');
             $this->form_validation->set_rules('student_id', translate('student'), 'trim|required');
             $this->form_validation->set_rules('staff_id', 'Teacher', 'trim|required');
            
                $this->form_validation->set_rules('subjects[]', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $branchID = $this->application_model->get_branch_id();
                    $arraySubject = array(
                        'class_id' => $this->input->post('class_id'),
                        'section_id' => $this->input->post('section_id'),
                        'session_id' => get_session_id(),
                        'branch_id' => $branchID,
                    	'student_id' => $this->input->post('student_id'),
                    	'subject_type' => 'extra',
                    );

                    // get class teacher details
                    $subject_assign_id = $this->input->post('subject_assign_id');
                    $subjects = $this->input->post('subjects');
                    foreach ($subjects as $subject) {
                        $arraySubject['subject_id'] = $subject;
                        //$query = $this->db->get_where("subject_assign", $arraySubject);
                        //if ($query->num_rows() == 0) {
                            $arraySubject['teacher_id'] = $this->input->post('staff_id');
                    		if(!empty( $subject_assign_id)){
                            $this->db->where('id',$subject_assign_id);
                             $this->db->update('subject_assign', $arraySubject);
                            } else {
                            $this->db->insert('subject_assign', $arraySubject);
                            }
                       // }
                    }
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $url = base_url('subject/class_assign');
                    $array = array('status' => 'success', 'url' => 'load_data', 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }


    // subject assign information edit
    public function class_assign_edit()
    {
        if ($_POST) {
            if (get_permission('subject_class_assign', 'is_edit')) {
                $this->form_validation->set_rules('subjects[]', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $sessionID = get_session_id();
                    $classID = $this->input->post('class_id');
                    $sectionID = $this->input->post('section_id');
                    $branchID = $this->application_model->get_branch_id();
                    $arraySubject = array(
                        'class_id' => $classID,
                        'section_id' => $sectionID,
                        'session_id' => $sessionID,
                        'branch_id' => $branchID,
                    );
                    // get class teacher details
                    $get_teacher = $this->subject_model->get('teacher_allocation', $arraySubject, true);

                    $subjects = $this->input->post('subjects');
                    foreach ($subjects as $subject) {
                        $arraySubject['subject_id'] = $subject;
                        $query = $this->db->get_where("subject_assign", $arraySubject);
                        if ($query->num_rows() == 0) {
                            $arraySubject['teacher_id'] = empty($get_teacher) ? 0 : $get_teacher['teacher_id'];
                            $this->db->insert('subject_assign', $arraySubject);
                        }
                    }
                    $this->db->where_not_in('subject_id', $subjects);
                    $this->db->where('class_id', $classID);
                    $this->db->where('section_id', $sectionID);
                    $this->db->where('session_id', $sessionID);
                    $this->db->where('branch_id', $branchID);
                    $this->db->delete('subject_assign');
                    set_alert('success', translate('information_has_been_updated_successfully'));
                    $url = base_url('subject/class_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }

    public function class_assign_delete($class_id = '', $section_id = '')
    {
        if (!get_permission('subject_class_assign', 'is_delete')) {
            access_denied();
        }
        if (!is_superadmin_loggedin()) {
            $this->db->where('branch_id', get_loggedin_branch_id());
        }
        $this->db->where('class_id', $class_id);
        $this->db->where('section_id', $section_id);
        $this->db->where('session_id', get_session_id());
        $this->db->delete('subject_assign');
    }

    // validate here, if the check class assign
    public function unique_subject_assign($class_id)
    {
        $where = array(
            'class_id' => $class_id,
            'section_id' => $this->input->post('section_id'),
            'session_id' => get_session_id(),
        );
        $q = $this->db->get_where('subject_assign', $where)->num_rows();
        if ($q == 0) {
            return true;
        } else {
            $this->form_validation->set_message('unique_subject_assign', 'This class and section is already assigned.');
            return false;
        }
    }

    // teacher assign view page
    public function teacher_assign()
    {
        if (!get_permission('subject_teacher_assign', 'is_view')) {
            access_denied();
        }
        if ($_POST) {
            if (get_permission('subject_teacher_assign', 'is_add')) {
                if (is_superadmin_loggedin()) {
                    $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
                }
                $this->form_validation->set_rules('staff_id', translate('teacher'), 'trim|required');
                $this->form_validation->set_rules('class_id', translate('class'), 'trim|required');
                $this->form_validation->set_rules('section_id', translate('section'), 'trim|required');
                $this->form_validation->set_rules('subject_id', translate('subject'), 'trim|required');
                if ($this->form_validation->run() !== false) {
                    $sessionID = get_session_id();
                    $branchID = $this->application_model->get_branch_id();
                    $classID = $this->input->post('class_id');
                    $sectionID = $this->input->post('section_id');
                    $subjectID = $this->input->post('subject_id');
                    $teacherID = $this->input->post('staff_id');
                    $query = $this->db->get_where("subject_assign", array(
                        'class_id' => $classID,
                        'section_id' => $sectionID,
                        'subject_id' => $subjectID,
                        'session_id' => $sessionID,
                        'branch_id' => $branchID,
                    ));
                    if ($query->num_rows() != 0) {
                        $this->db->where('id', $query->row()->id);
                        $this->db->update('subject_assign', array('teacher_id' => $teacherID));
                    }
                    set_alert('success', translate('information_has_been_updated_successfully'));
                    $url = base_url('subject/teacher_assign');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['assignlist'] = $this->subject_model->getTeacherAssignList();
        $this->data['title'] = translate('teacher_assign');
        $this->data['sub_page'] = 'subject/teacher_assign';
        $this->data['main_menu'] = 'subject';
        $this->load->view('layout/index', $this->data);
    }

    // teacher assign information moderator
    public function teacher_assign_delete($id = '')
    {
        if (get_permission('subject_teacher_assign', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->update('subject_assign', array('teacher_id' => 0));
        }
    }

    // get subject list based on class section
    public function getByClassSection()
    {
        $html = '';
        $classID = $this->input->post('classID');
        $sectionID = $this->input->post('sectionID');
        if (!empty($classID)) {
            $query = $this->db->select('id,subject_id')->where(array('class_id' => $classID, 'section_id' => $sectionID))->get('subject_assign');
            if ($query->num_rows() != 0) {
                $html .= '<option value="">' . translate('select') . '</option>';
                $subjects = $query->result_array();
                foreach ($subjects as $row) {
                    $html .= '<option value="' . $row['subject_id'] . '">' . get_type_name_by_id('subject', $row['subject_id']) . '</option>';
                }
            } else {
                $html .= '<option value="">' . translate('no_information_available') . '</option>';
            }
        } else {
            $html .= '<option value="">' . translate('select') . '</option>';
        }
        echo $html;
    }
	
	public function edit_extra_subject(){
    	 $id = $this->input->post('id');	
    	$extra_subject = $this->subject_model->get_subject_extra_by_id($id);
    	if(count($extra_subject)){
    		 $class_id = $extra_subject[0]->class_id;
            $section_id = $extra_subject[0]->section_id;
            $subject_id = $extra_subject[0]->subject_id;
            $teacher_id = $extra_subject[0]->teacher_id;
            $branch_id = $extra_subject[0]->branch_id;
            $student_id = $extra_subject[0]->student_id;
        	$session_id =  $extra_subject[0]->session_id;
        }
    
    
    	$this->db->select('e.student_id,e.roll, CONCAT(s.first_name, " ", s.last_name) as fullname');
        $this->db->from('enroll as e');
        $this->db->join('student as s', 'e.student_id = s.id', 'inner');
        $this->db->join('login_credential as l', 'l.user_id = s.id and l.role = 7', 'inner');
        $this->db->join('class as c', 'e.class_id = c.id', 'left');
        $this->db->join('section as se', 'e.section_id=se.id', 'left');
        $this->db->where('e.class_id', $class_id);
        $this->db->where('e.branch_id', $section_id);
        $this->db->where('e.session_id', $session_id);
        if ($rollOrder == true) {
            $this->db->order_by('e.roll', 'ASC');
        } else {
            $this->db->order_by('s.id', 'ASC');
        }
        if ($sectionID != 'all') {
            $this->db->where('e.section_id', $section_id);
        }
        if ($deactivate == true) {
            $this->db->where('l.active', 0);
        }
        
            $student_result = array();
            $result = $this->db->get()->result_array();
        if (count($result)) {
               
                foreach ($result as $row) {
                    
                    $student_result[$row['student_id']] = $row['fullname'] . ' ( Roll : ' . $row['roll'] . ')';
                }
            }
    
    
    
   		$html = '<input type="hidden" name="subject_assign_id" value="'.$id.'" >';
    
    
    
		$html .='<div class="form-group">
						<label class=" control-label">'.translate('branch').'<span class="required">*</span></label>';
                    	
						
							$arrayBranch = $this->app_lib->getSelectList('branch');
                        	//print_r($arrayBranch);
							$html .= form_dropdown("branch_id", $arrayBranch, $student_id, "class='form-control' id='extra_branch_id'
							data-width='100%' data-plugin-selectTwo data-minimum-results-for-search='Infinity'");
                        	
						
						$html .= '<span class="error"></span>  </div>';
    
    		$html .='<div class="form-group">
						<label class=" control-label">'.translate('class').'<span class="required">*</span></label>';
                   		 
						
							$arrayClass = $this->app_lib->getClass($branch_id);
                        	
							$html .= form_dropdown("class_id", $arrayClass, $class_id, " class='form-control' id='extra_class_id' onchange='getSectionByClassForExtraSubject(this.value,0)'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						
						$html .='<span class="error"></span>
                    	
					</div>';
    
    	$html .='<div class="form-group">
						<label class=" control-label">'.translate('section').' <span class="required">*</span></label>';
                    	
						
							 $arraySection = $this->app_lib->getSections($class_id);
							$html .= form_dropdown("section_id", $arraySection, $section_id, "class='form-control' id='extra_section_id' onchange='getStudentByClassForExtraSubject(this.value)'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						
						$html .= '<span class="error"></span>
                    	
					</div>';
    		$html .='<div class="form-group">
						<label class=" control-label">Student <span class="required">*</span></label>';
                    	
						$html .= form_dropdown("student_id", $student_result, $student_id, "class='form-control' id='extra_student_id' 
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						
						$html .= '<span class="error"></span>
						
                    	
					</div>';
    		$html .='	<div class="form-group mb-md">
						<label class=" control-label">'.translate('class_teacher').' <span class="required">*</span></label>';
                    	
						
							$arrayTeacher = $this->app_lib->getStaffList($branch_id, 3);
							$html .= form_dropdown("staff_id", $arrayTeacher, $teacher_id, "class='form-control' id='staff_id'
							data-plugin-selectTwo data-width='100%' ");
					
						$html .= '<span class="error"></span>
                    	
					</div>';

				$html .='<div class="form-group" id="subjectbox">
						
						
					</div>';
    
    
    	echo $html;
    }
}
