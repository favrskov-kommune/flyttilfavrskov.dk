<?php
/**
 * Created by PhpStorm.
 * User: mma
 * Date: 03/11/2017
 * Time: 14.29
 */

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use Deployer\Utility\Httpie;

desc('Custom tasks to handle failed deployment');
task('slack:notify:failed', function () {
  if (!get('slack_webhook', false)) {
    return;
  }

  set('slack_text', 'Deploy to *{{target}}* failed');

  $attachment = [
    'title' => get('slack_title'),
    'text' => get('slack_text'),
    'color' => 'CF423F',
    'mrkdwn_in' => ['text'],
  ];

  Httpie::post(get('slack_webhook'))->body(['attachments' => [$attachment]])->send();
})
  ->once()
  //  ->shallow()
  ->setPrivate()
;

desc('Custom tasks to initiate slack messages');
task('slack:notify:init', function () {
  set('slack_webhook', 'https://hooks.slack.com/services/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');

  set('slack_color', 'E6E6E6');

  $git_name = runLocally('git config --get user.name');
  $git_email = runLocally('git config --get user.email');
  if(empty($git_name) && empty($git_email)){
    throw new GracefulShutdownException('You need to specify Git user.name and user.email in your Git configuration.');
  }
  $git_user = !empty($git_name) ? (!empty($git_email) ? $git_name.' <'.$git_email.'>' : $git_name) : $git_email;
  set('slack_text', '_'.$git_user.'_ is deploying `{{branch}}` branch to *{{target}}*');

})
  ->once()
  ->shallow()
  ->setPrivate()
;

task('slack:notify:success:init', function(){
  set('slack_color', '4AB441');
})
  ->once()
  ->shallow()
  ->setPrivate();


