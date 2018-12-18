<?php
namespace hwong02 {
    class email {
        private $sendto = NULL;
        private $from = NULL;
        private $text = NULL;
        private $html = NULL;
        private $boundary = NULL;
        private $subject = NULL;
        private $replyTo = NULL;
        private $cc = NULL;
        private $attachment = array();

        function __construct() {
            $this->boundary = md5(uniqid('np', true));
        }

        function __toString() {    
            $message = $this->getMessage();
            $header = NULL;
            $header .= (! is_null($this->sendto)) ? "To: ".$this->sendto."\r\n" : NULL;
            $header .= $this->getHeader($message);
            $header .= (! is_null($header)) ? "\r\n" : NULL;
            return $header . $message;
        }
        
        private function getHeader($message = NULL) {
            $header = NULL;

            if (! is_null($this->from)) { 
                $header = "From: ".$this->from."\r\n";
            };
            if (! is_null($this->cc)) { 
                $header = "CC: ".$this->cc."\r\n";
            };

            if (! is_null($message)) {
                $header .= "MIME-Version: 1.0\r\n";
            };
            if (! is_null($message)) {
                $header .= "Content-Type: multipart/alternative;boundary=".$this->boundary."\r\n";
            };
            if (! is_null($this->replyTo)) {
                $header .= "Reply-To: ".$this->replyTo."\r\n";
            };

            return $header;
        }

        private function getMessage() {
            $message = NULL;
            if (! is_null($this->text)) {
                # code...
                $message = ($message) ?? "This is a MIME encoded message.";
                $message .= "\r\n\r\n--" . $this->boundary . "\r\n";
                $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
                $message .= $this->text;
            }

            if (! is_null($this->html)) {
                # code...
                $message = ($message) ?? "This is a MIME encoded message.";
                $message .= "\r\n\r\n--" . $this->boundary . "\r\n";
                $message .= "Content-Type: text/html;charset=utf-8\r\n\r\n";
                $message .= $this->html;
            }
            $this->attachment=array_unique($this->attachment);
            if (count($this->attachment) > 0) {
                $message = ($message) ?? "This is a MIME encoded message.";
                foreach ($this->attachment as $filename) {
                    if (file_exists($filename)) {
                        $path = pathinfo($filename);
                        $message .= "\r\n\r\n--" . $this->boundary . "\r\n";
                        $message .= "Content-Type: application/octet-stream; name=\"".$path['filename']."\"\r\n";
                        $message .= "Content-Transfer-Encoding: base64\r\n";
                        $message .= "Content-Disposition: attachment; filename=\"".$path['filename']."\"\r\n\r\n";
                        $content = chunk_split(base64_encode(file_get_contents($filename)));
                        $message .= $content;
                    }
                }
            }
            if (! is_null($message)) {
                $message .= "\r\n\r\n--" . $this->boundary . "--";
            } 
            return $message;
        }

        public function sendEmail($smtp = NULL, $port = NULL) {
            //echo $this->getHeader();
            // echo $this->getMessage();
            //exit;
            if (! is_null($smtp)) {
                ini_set('SMTP', $smtp);
            };
            if (! is_null($port)) {
                ini_set('smtp_port', $port);
            };
            $message = $this->getMessage();
            $body = (! is_null($message)) ? 1 : 0;
            if (mail($this->sendto, $this->subject, $message, $this->getHeader($body))) {
                return true;
            } else {
                return false;
                /*
                echo "<br> Failed to send email.<br>";
                $error = error_get_last();
                var_dump($error);
                echo("<html><body><div><h2 style=\"width:800px; margin:40px auto; text-align:center; color:#FF0000;\">Failed to send email!</h2></div></body></html>");
                */
            }
        }

        public function attach($file) {
            $this->attachment[] = $file;
            return $this;
        }

        public function deAttach($file) {
            $this->attachment = array_diff($this->attachment, array($file));
            return $this;
        }

        // set function
        public function clearAttach() {
            $this->attachment[] = array();
            return $this;
        }

        public function textFile($file) {
            $this->text = file_get_contents($file);
            return $this;
        }

        public function htmlFile($file) {
            $this->html = file_get_contents($file);
            return $this;
        }

        public function to($sendto) {
            $this->sendto = trim($sendto);
            return $this;
        }

        public function from($from) {
            $this->from = trim($from);
            return $this;
        }

        public function textMessage($body) {
            $this->text = $body;
            return $this;
        }

        public function htmlMessage($body) {
            $this->html = $body;
            return $this;
        }

        public function subject($subject) {
            $this->subject = $subject;
            return $this;
        }

        public function replyTo($replyTo) {
            $this->replyTo = $replyTo;
            return $this;
        }

        public function cc($cc) {
            $this->cc = $cc;
            return $this;
        }
    }
}

/*
namespace {
    $o = new hwong02\email();

    // email
    $o->from('somethingweird@yahoo.com')
        ->to('anyone@yahoo.com')
        ->subject('test email2')
        ->htmlMessage('html hello')
        ->textMessage('text hello');

    // read from file
    
    //    $o->htmlFile('nameoffile.html')->textFile('nameoffile.txt');

    // show what the email is suppose to look like

    // send it out thru NYIT servers
    $o->sendEmail('smtp.yahoo.com', 25);

    $o->subject('test email3');
    $o->attach('attach1.txt')->attach('attach2.txt');
    $o->sendEmail('smtp.yahoo.com', 25);

    // remove 1 attachment NOT TESTED yet
    $o->htmlMessage('differt messgage');
    $o->deAttach('attach1.txt');

    // send again
    $o->sendEmail('smtp.yahoo.com', 25);

    // clear all attachments
    $o->clearAttach();
}
*/