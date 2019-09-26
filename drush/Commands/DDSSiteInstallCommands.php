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
      $dds_project_name = $this->io()->ask('Project name (used for creating sites.php)', 'premium');
      $dds_project_domain = $this->io()->ask('Project domain (used for creating sites folder)', 'premium.test');
      $profile = [
        'dds_premium_installer',
        'dds_installer_configuration_form.project_name='.$dds_project_name,
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


