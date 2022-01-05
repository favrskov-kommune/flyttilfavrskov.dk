<?php
/* (c) Mikkel Mandal <mma@novicell.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;

task('deploy:db:set_backup_file', function () {
  set('sql_backup_folder_path', '{{deploy_path}}/sql');
  $release = get('release_name');
  set('current_sql_backup', get('sql_backup_folder_path').'/backup_'.$release.'.sql');
})
  ->once()
  ->shallow()
  ->setPrivate();

desc('Dump current database');
task('deploy:db:dump', function () {
  $current_sql_backup = get('current_sql_backup');

  $current_ptag_sql_backup = str_replace('/backup_', '/ptag_backup_', $current_sql_backup);
  $current_energi_sql_backup = str_replace('/backup_', '/energi_backup_', $current_sql_backup);
  run('if [ ! -d ' . get('sql_backup_folder_path') . ' ]; then mkdir -p ' . get('sql_backup_folder_path') . '; fi');
  run('cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag cr && drush @ptag sql-dump --structure-tables-list=cache,cache_* > ' . $current_ptag_sql_backup);
  run('cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi cr && drush @energi sql-dump --structure-tables-list=cache,cache_* > ' . $current_energi_sql_backup);
})
  ->setPrivate();

desc('Rollback database');
task('deploy:db:rollback', function () {
  $rollback_db = get('rollback_db');

  $current_sql_backup = get('current_sql_backup');

  $current_ptag_sql_backup = str_replace('/backup_', '/ptag_backup_', $current_sql_backup);
  $new_ptag_sql_backup = str_replace('/ptag_backup_','/rollback_'.date('YmdHis').'_ptag_backup_', $current_ptag_sql_backup);
  $current_energi_sql_backup = str_replace('/backup_', '/energi_backup_', $current_sql_backup);
  $new_energi_sql_backup = str_replace('/energi_backup_','/rollback_'.date('YmdHis').'_energi_backup_', $current_energi_sql_backup);

  if($rollback_db == 'true') {

    if(!empty($current_ptag_sql_backup)){
      run('if [ -f ' . $current_ptag_sql_backup . ' ]; then cd {{deploy_path}}/current/webroot/sites/phonixtag.dk && drush @ptag sql-drop -y && drush @ptag sql-cli < '.$current_ptag_sql_backup.' && mv '.$current_ptag_sql_backup.' '.$new_ptag_sql_backup.'; fi');
      writeln('Database rolled back to: '.$current_ptag_sql_backup);
      writeln('Backup file was renamed: '.$new_ptag_sql_backup);
    } else {
      writeln('No available database backup: '.$current_ptag_sql_backup);
    }

    if(!empty($current_energi_sql_backup)){
      run('if [ -f ' . $current_energi_sql_backup . ' ]; then cd {{deploy_path}}/current/webroot/sites/phonixtagenergi.dk && drush @energi sql-drop -y && drush @energi sql-cli < '.$current_energi_sql_backup.' && mv '.$current_energi_sql_backup.' '.$new_energi_sql_backup.'; fi');
      writeln('Database rolled back to: '.$current_energi_sql_backup);
      writeln('Backup file was renamed: '.$new_energi_sql_backup);
    } else {
      writeln('No available database backup: '.$current_energi_sql_backup);
    }
  } else {
    if(!empty($current_ptag_sql_backup)){
      run('if [ -f ' . $current_ptag_sql_backup . ' ]; then mv '.$current_ptag_sql_backup.' '.$new_ptag_sql_backup.'; fi');
      writeln('No database rollback necessary. Backup file was renamed: '.$new_ptag_sql_backup);
    } else {
      writeln('No available database backup: '.$current_ptag_sql_backup);
    }

    if(!empty($current_energi_sql_backup)){
      run('if [ -f ' . $current_energi_sql_backup . ' ]; then mv '.$current_energi_sql_backup.' '.$new_energi_sql_backup.'; fi');
      writeln('No database rollback necessary. Backup file was renamed: '.$new_energi_sql_backup);
    } else {
      writeln('No available database backup: '.$current_energi_sql_backup);
    }
  }
})
  ->setPrivate();

desc('Cleanup old database backups');
task('deploy:db:cleanup', function () {
  $releases = get('releases_list');
  $keep = get('keep_releases');
  $sudo  = get('cleanup_use_sudo') ? 'sudo' : '';

  if ($keep === -1) {
    // Keep unlimited releases.
    return;
  }

  while ($keep > 0) {
    array_shift($releases);
    --$keep;
  }

  foreach ($releases as $release) {
    $sql_backup = get('sql_backup_folder_path').'/ptag_backup_'.$release.'.sql';
    run('if [ -f ' . $sql_backup . ' ]; then '.$sudo.' rm -rf '.$sql_backup.'; fi');

    $sql_backup = get('sql_backup_folder_path').'/energi_backup_'.$release.'.sql';
    run('if [ -f ' . $sql_backup . ' ]; then '.$sudo.' rm -rf '.$sql_backup.'; fi');
  }
})
  ->setPrivate();

before('deploy:db:dump','deploy:db:set_backup_file');
before('deploy:db:rollback','deploy:db:set_backup_file');
before('deploy:db:cleanup','deploy:db:set_backup_file');