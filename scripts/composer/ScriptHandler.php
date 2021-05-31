<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use DirectoryIterator;
use Composer\Semver\Comparator;
use Composer\Script\Event;

class ScriptHandler {

  protected static function getDrupalRoot($project_root) {
    return $project_root . '/webroot';
  }

  /**
   * Checks if the installed version of Composer is compatible.
   *
   * Composer 1.0.0 and higher consider a `composer install` without having a
   * lock file present as equal to `composer update`. We do not ship with a lock
   * file to avoid merge conflicts downstream, meaning that if a project is
   * installed with an older version of Composer the scaffolding of Drupal will
   * not be triggered. We check this here instead of in drupal-scaffold to be
   * able to give immediate feedback to the end user, rather than failing the
   * installation after going through the lengthy process of compiling and
   * downloading the Composer dependencies.
   *
   * @see https://github.com/composer/composer/pull/5035
   */
  public static function checkComposerVersion(Event $event) {
    $composer = $event->getComposer();
    $io = $event->getIO();

    $version = $composer::VERSION;

    // The dev-channel of composer uses the git revision as version number,
    // try to the branch alias instead.
    if (preg_match('/^[0-9a-f]{40}$/i', $version)) {
      $version = $composer::BRANCH_ALIAS_VERSION;
    }

    // If Composer is installed through git we have no easy way to determine if
    // it is new enough, just display a warning.
    if ($version === '@package_version@' || $version === '@package_branch_alias_version@') {
      $io->writeError('<warning>You are running a development version of Composer. If you experience problems, please update Composer to the latest stable version.</warning>');
    } elseif (Comparator::lessThan($version, '1.0.0')) {
      $io->writeError('<error>Drupal-project requires Composer version 1.0.0 or higher. Please update your Composer before continuing</error>.');
      exit(1);
    }
  }

  /**
   * Interactive confirmation for the user/developer to delete or keep .git files
   *
   * @param Event $event
   */
  public static function askForRemovingGitDirs(Event $event) {
//    if ($event->getIO()->askConfirmation('<question>Do you want to delete all .git dirs in (vendor/, webroot/modules/, webroot/themes/, webroot/libraries/, webroot/profiles/) [Y/n] - (default is Y) ? </question>', TRUE)) {
      shell_exec('cd vendor/ && find . -type d | grep .git | xargs rm -rf');
      shell_exec('cd webroot/modules/ && find . -type d | grep .git | xargs rm -rf');
      shell_exec('cd webroot/themes/ && find . -type d | grep .git | xargs rm -rf');
      shell_exec('cd webroot/libraries/ && find . -type d | grep .git | xargs rm -rf');
      shell_exec('cd webroot/profiles/ && find . -type d | grep .git | xargs rm -rf');
      $event->getIO()->write("<info>Now all .git dirs in (vendor/, webroot/modules/, webroot/themes/, webroot/libraries/, webroot/profiles/) are deleted!</info>");
//    }
  }

  /**
   * Recursively copy files from one directory to another
   *
   * @param string $src
   *    Source of files being moved.
   *
   * @param string $dest
   *    Destination of files being moved.
   *
   * @return bool
   */
  public static function rcopy($src, $dest) {
    // If source is not a directory stop processing
    if (!is_dir($src)) {
      return false;
    }

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
      if (!mkdir($dest, 0777, true)) {
        // If the destination directory could not be created stop processing
        return false;
      }
    }

    // Open the source directory to read in files
    // Adding context for the installer
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
      //    $context['sandbox']['progress']++;
      if ($f->isFile()) {
        copy($f->getRealPath(), "$dest/" . $f->getFilename());
      } else {
        if (!$f->isDot() && $f->isDir()) {
          static::rcopy($f->getRealPath(), "$dest/$f");
        }
      }
    }
    return true;
  }
}
