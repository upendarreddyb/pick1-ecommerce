<?php
namespace App\Libraries;
use App\Models\OtpLoginModel;
class Otp {
 public function send(string $email): bool|string { $m=new OtpLoginModel(); $last=$m->where('email',$email)->orderBy('id','DESC')->first(); if($last && strtotime($last['created_at'])>time()-60) return 'Please wait 60 seconds before requesting another code.'; $m->where('email',$email)->where('is_used',0)->set(['is_used'=>1])->update(); $code=(string)random_int(100000,999999); $m->insert(['email'=>$email,'otp_code'=>password_hash($code,PASSWORD_DEFAULT),'expires_at'=>date('Y-m-d H:i:s',time()+600),'created_at'=>date('Y-m-d H:i:s')]); $emailService=service('email'); $emailService->setTo($email)->setSubject('Your sign-in code')->setMessage("Your verification code is {$code}. It expires in 10 minutes."); return $emailService->send() ? true : 'The email could not be sent. Check the SMTP settings.'; }
 public function verify(string $email,string $code): bool { $m=new OtpLoginModel(); $row=$m->where('email',$email)->where('is_used',0)->where('expires_at >',date('Y-m-d H:i:s'))->orderBy('id','DESC')->first(); if(!$row || !password_verify($code,$row['otp_code'])) return false; $m->update($row['id'],['is_used'=>1]); return true; }
}
