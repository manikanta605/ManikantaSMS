<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class App extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->load->model("staff_model");
        $this->load->library('Auth');
        $this->load->library('Enc_lib');
        $this->load->library('customlib');
        $this->load->library('mailer');
        $this->load->config('ci-blog');

    }

    public function index()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $setting_result = $this->setting_model->getSetting();

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(
                    array(
                        'url' => $setting_result->mobile_api_url,
                        'site_url' => site_url(),
                        'app_logo' => $setting_result->app_logo,
                        'app_primary_color_code' => $setting_result->app_primary_color_code,
                        'app_secondary_color_code' => $setting_result->app_secondary_color_code,
                        'lang_code' => $setting_result->language_code,
                    )
                ));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(405)
                ->set_output(json_encode(
                    array(
                        'error' => "Method Not Allowed",
                    )
                ));
        }

    }

    public function zoom()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.zoom.us/v2/users?status=active&page_size=30&page_number=1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer sY6xc8tAS7Wj8-MXyXxheg",
                "content-type: application/json",
            ),
        )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

    }
    public function loginFunction()
    {
        // if ($this->input->server('REQUEST_METHOD') == 'POST') {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules('username', $this->lang->line('username'), 'trim|required|xss_clean');
            $this->form_validation->set_rules('password', $this->lang->line('password'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == FALSE) {
                // $this->load->view('userlogin', $data);
                $array = array(
                    'error' => true,
                );
            } else {
                $login_post = array(
                    'username' => $this->input->post('username'),
                    'password' => $this->input->post('password')
                );
                $login_details = $this->user_model->checkLogin($login_post);
                if (isset($login_details) && !empty($login_details)) {
                    $user = $login_details[0];
                    if ($user->is_active == "yes") {
                        if ($user->role == "student") {
                            $result = $this->user_model->read_user_information($user->id);
                        } else if ($user->role == "parent") {
                            $result = $this->user_model->checkLoginParent($login_post);
                        }

                        if ($result != false) {
                            $setting_result = $this->setting_model->get();
                            if ($result[0]->lang_id == 0) {
                                $language = array('lang_id' => $setting_result[0]['lang_id'], 'language' => $setting_result[0]['language']);
                            } else {
                                $language = array('lang_id' => $result[0]->lang_id, 'language' => $result[0]->language);
                            }
                            if ($result[0]->role == "parent") {
                                $username = $result[0]->guardian_name;
                                if ($result[0]->guardian_relation == "Father") {
                                    $image = $result[0]->father_pic;
                                } else if ($result[0]->guardian_relation == "Mother") {
                                    $image = $result[0]->mother_pic;
                                } else if ($result[0]->guardian_relation == "Other") {
                                    $image = $result[0]->guardian_pic;
                                }
                            } elseif ($result[0]->role == "student") {
                                $image = $result[0]->image;
                                $username = ($result[0]->lastname != "") ? $result[0]->firstname . " " . $result[0]->lastname : $result[0]->firstname;
                            }



                            $session_data = array(
                                'id' => $result[0]->id,
                                'student_id' => $result[0]->user_id,
                                'role' => $result[0]->role,
                                'username' => $username,
                                'date_format' => $setting_result[0]['date_format'],
                                'currency_symbol' => $setting_result[0]['currency_symbol'],
                                'timezone' => $setting_result[0]['timezone'],
                                'sch_name' => $setting_result[0]['name'],
                                'language' => $language,
                                'is_rtl' => $setting_result[0]['is_rtl'],
                                'theme' => $setting_result[0]['theme'],
                                'image' => $result[0]->image,
                            );
                            $this->session->set_userdata('student', $session_data);

                            // $student_display_session = $this->studentsession_model->searchActiveClassSectionStudent($result[0]->user_id);
                            // $student_current_class = array('student_session_id'=>$student_display_session->id,'class_id' => $student_display_session->class_id,
                            //     'section_id' => $student_display_session->section_id);

                            // $this->session->set_userdata('current_class', $student_current_class);

                            $this->customlib->setUserLog($result[0]->username, $result[0]->role);
                            // redirect('user/user/dashboard');
                            // redirect('user/user/choose');
                            // $this->response(['status' => $session_data], REST_Controller::HTTP_CREATED);
                            $data['error_message'] = $this->lang->line('invalid_username_or_password');
                            $array = array(
                                'data' => $session_data,

                            );

                        } else {
                            $data['error_message'] = 'Account Suspended';
                            $data['error_message'] = $this->lang->line('invalid_username_or_password');
                            $array = array(
                                'error' => $data,

                            );
                        }
                    } else {
                        $data['error_message'] = $this->lang->line('your_account_is_disabled_please_contact_to_administrator');
                        // $this->load->view('userlogin', $data);
                        $this->response(['status' => $data], REST_Controller::HTTP_CREATED);
                    }
                } else {
                    $data['error_message'] = $this->lang->line('invalid_username_or_password');
                    $array = array(
                        'error' => $data,

                    );
                }


                // $login_details = $this->user_model->checkLogin($login_post);
                // $array = array(
                //     'error'    => $login_post,
                // );

            }
        } else {
            $array = array(
                'error' => true,
                'first_name_error1' => form_error('first_name1111'),
                'last_name_error1' => form_error('last_name11111')
            );
        }
        // echo json_encode($array, true);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(405)
            ->set_output(json_encode(
                array(
                    'data' => $array
                )
            ));
        // echo json_decode($array, true)
    }

}