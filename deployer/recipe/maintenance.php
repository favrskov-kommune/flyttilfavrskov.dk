<?php
/* (c) Mikkel Mandal <mma@novicell.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

desc('Enable maintenance mode');
task('deploy:maintenance_mode:enable', function () {
  run("cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag sset system.maintenance_mode 1");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag cr");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi sset system.maintenance_mode 1");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi cr");
})
  ->setPrivate();

desc('Disable maintenance mode');
task('deploy:maintenance_mode:disable', function () {
  run("cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag sset system.maintenance_mode 0");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag cr");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi sset system.maintenance_mode 0");
  run("cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi cr");
})
  ->setPrivate();
