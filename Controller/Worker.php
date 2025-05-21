<?php

require_once 'Controller.php';
require_once 'Services/Notification/Notify.php';

class Worker extends Controller
{
    private $notify;

    public function __construct()
    {
        parent::__construct();
        $this->notify = new Notify();
    }

    public function processNotifications()
    {
        $notifications = $this->resarvation->GetNotify();
        $message = [];

        foreach ($notifications as $row) {
            //
            $result = $this->notify->sendFCM($row['fcm_token'], $row['title'], $row['message']);
            //
            $id_notify = $row['id'];
            if ($result['success']) {
                $this->resarvation->UpdateStatus($row['id'], 'done');
            } else {
                $this->resarvation->UpdateStatus($row['id'], 'fail');
                $message[] = "can't send notification to $id_notify ";
            }
        }

        return ["Finished processing notifications.", $message];
    }


    public function WorkerStart()
    {
        while (true) {
            // 
            $notifications = $this->resarvation->GetNotify();
            $message = [];
            //
            if (!empty($notifications)) {
                foreach ($notifications as $row) {
                    $id_notify = $row['id'];

                    // 
                    $result = $this->notify->sendFCM($row['fcm_token'], $row['title'], $row['message']);

                    //
                    if ($result['success']) {
                        $this->resarvation->UpdateStatus($id_notify, 'done');
                        echo "Notification ID: $id_notify processed successfully.\n";
                    } else {
                        $this->resarvation->UpdateStatus($id_notify, 'fail');
                        $message[] = "Can't send notification to ID: $id_notify";
                        echo "Failed to send notification ID: $id_notify.\n";
                    }
                }
            } else {
                echo "No new notifications. Sleeping for 2 minutes...\n";
            }
            // 
            sleep(900);
        }
    }
}
