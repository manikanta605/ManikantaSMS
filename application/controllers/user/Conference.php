<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conference extends Student_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('conference_model', 'conferencehistory_model'));
    }

    public function index() {
        $this->session->set_userdata('top_menu', 'Conference');
        $data = array();
        $student_current_class = $this->customlib->getStudentCurrentClsSection();

        $list = $this->conference_model->getByClassSection($student_current_class->class_id, $student_current_class->section_id);

        $data['conferences'] = $list;

        $this->load->view('layout/student/header');
        $this->load->view('user/conference/timetable', $data);
        $this->load->view('layout/student/footer');
    }

    public function add_history() {

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'id' => form_error('id'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $student_id = $this->customlib->getStudentSessionUserID();
            $data_insert = array(
                'conference_id' => $this->input->post('id'),
                'student_id' => $student_id,
            );

            $this->conferencehistory_model->updatehistory($data_insert, 'student');
            $array = array('status' => 1, 'error' => '');
            echo json_encode($array);
        }
    }

}
