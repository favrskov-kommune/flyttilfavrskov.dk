<?php
/* (c) Mikkel Mandal <mma@novicell.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;


desc('Run composer install');
task('deploy:composer:install', function () {

  run('cd {{release_path}} && composer install -n -o');
})
  ->setPrivate();


