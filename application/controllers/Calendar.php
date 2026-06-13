<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Calendar extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CalendarModel');
        $this->load->helper('url');
        $this->load->library('session');

        if ($this->session->userdata('logged_in') !== TRUE) {
            redirect('login');
        }
    }

    public function index()
    {

        $username = $this->session->userdata('username'); // Get logged-in user's username
        $events = $this->CalendarModel->getEvents($username); // Get user + public events

        $data['events'] = $events;

        $this->load->view('calendar', $data);
    }


    public function fetch_events()
    {
        $username = $this->session->userdata('username'); // Get the logged-in user's username

        $this->db->group_start();
        $this->db->where('username', $username);
        $this->db->or_where('status', 'public');
        $this->db->group_end();

        $events = $this->db->get('calendar_events')->result();
        $data = [];

        foreach ($events as $event) {
            $data[] = array(
                'id'     => $event->id,
                'title'  => $event->title,
                'start'  => $event->start,
                'end'    => $event->end,
                'status' => $event->status,
                'color'  => $event->color
            );
        }

        echo json_encode($data);
    }


    public function add_event()
    {
        $username = $this->session->userdata('username');

        if (!$username) {
            log_message('error', 'Username not found in session.');
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
            return;
        }

        $data = array(
            'title'    => $this->input->post('title'),
            'start'    => $this->input->post('start'),
            'end'      => $this->input->post('end'),
            'status'   => $this->input->post('status'),
            'username' => $username
        );

        $success = $this->CalendarModel->addEvent($data);

        if ($success) {
            echo json_encode(['status' => 'success']);
        } else {
            log_message('error', 'Failed to insert calendar event: ' . json_encode($data));
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert']);
        }
    }


    public function update_event()
    {
        $id = $this->input->post('id');
        $username = $this->session->userdata('username');

        $data = array(
            'title' => $this->input->post('title'),
            'start' => $this->input->post('start'),
            'end'   => $this->input->post('end')
        );

        $this->CalendarModel->updateEvent($id, $data, $username);
        echo json_encode(['status' => 'updated']);
    }

    public function delete_event()
    {
        $id = $this->input->post('id');
        $username = $this->session->userdata('username');

        $this->CalendarModel->deleteEvent($id, $username);
        echo json_encode(['status' => 'deleted']);
    }

    public function save_event()
    {
        $username = $this->session->userdata('username');

        if (!$username) {
            log_message('error', 'Username not found in session.');
            redirect('calendar'); // or redirect to login page if session expired
            return;
        }

        $data = array(
            'title'    => $this->input->post('title'),
            'start'    => $this->input->post('start'),
            'end'      => $this->input->post('end'),
            'status'   => $this->input->post('status'),
            'color'    => $this->input->post('color'),
            'username' => $username // ✅ Save username from session
        );

        $this->db->insert('calendar_events', $data);
        redirect('calendar');
    }
}
