# Email Builder

## Introduction

A high level php library from sending e-mail.

## Code Samples

<?php
require('buildmail.php');

$o = new hwong02\email();

$o->from('somethingweird@yahoo.com')

->to('anyone@yahoo.com')

->subject('test email2')

->htmlMessage('html hello')

 ->textMessage('text hello')

 ->attach('somefile')  

->sendEmail('smtp.yahoo.com', 25);


## Installation

just copy buildmail.php into your directory