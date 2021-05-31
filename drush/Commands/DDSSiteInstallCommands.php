<?php

namespace Drush\Commands;

use Consolidation\AnnotatedCommand\AnnotationData;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DDSSiteInstallCommands extends DrushCommands
{

  /**
   * @hook interact site-install
   */
  public function interact(InputInterface $input, OutputInterface $output, AnnotationData $annotationData)
  {
    $profile = $input->getArgument('profile');
    if(in_array('dds_premium', $profile)) {
      if(getenv('SITES_FOLDER')) {
        //this is probably running in a docker container
        $dds_project_domain = getenv('SITES_FOLDER');
        $input->setOption('db-url', 'mysql://'.getenv('DB_USER').':'.getenv('DB_PASS').'@'.getenv('DB_HOST').':'.getenv('DB_PORT').'/'.getenv('DB_SCHEMA'));
        $input->setOption('sites-subdir', $dds_project_domain);

      } else {
        //this is probably not a docker container
        $dds_project_domain = $this->io()->ask('Project domain (used for creating sites folder)', 'premium.test');
        $input->setOption('sites-subdir', $dds_project_domain);
        $input->setOption('db-url', 'mysql://root:root@127.0.0.1:3306/premium');
      }
      $profile = [
        'dds_premium_installer',
        'dds_installer_configuration_form.project_domain='.$dds_project_domain,
        'install_configure_form.enable_update_status_module=NULL',
      ];
      $input->setArgument('profile', $profile);

      $input->setOption('account-name', 'novicell');
      $input->setOption('account-mail', 'php@novicell.dk');
      $input->setOption('site-mail', 'php@novicell.dk');
      $input->setOption('site-name', 'DDS Premium Website');
    }

  }


}


