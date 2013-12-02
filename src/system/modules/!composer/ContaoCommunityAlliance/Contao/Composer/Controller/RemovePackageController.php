<?php

namespace ContaoCommunityAlliance\Contao\Composer\Controller;

use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\Console\HtmlOutputFormatter;
use Composer\IO\BufferIO;
use Composer\Json\JsonFile;
use Composer\Package\BasePackage;
use Composer\Package\PackageInterface;
use Composer\Package\RootPackageInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Util\ConfigValidator;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Solver;
use Composer\DependencyResolver\Request;
use Composer\DependencyResolver\SolverProblemsException;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Repository\InstalledArrayRepository;

/**
 * Class RemovePackageController
 */
class RemovePackageController extends AbstractController
{
	/**
	 * {@inheritdoc}
	 */
	public function handle(\Input $input)
	{
		$removeName = $input->post('remove');

		// make a backup
		copy(TL_ROOT . '/' . $this->configPathname, TL_ROOT . '/' . $this->configPathname . '~');

		// update requires
		$json   = new JsonFile(TL_ROOT . '/' . $this->configPathname);
		$config = $json->read();
		if (!array_key_exists('require', $config)) {
			$config['require'] = array();
		}
		unset($config['require'][$removeName]);
		$json->write($config);

		$_SESSION['TL_INFO'][] = sprintf(
			$GLOBALS['TL_LANG']['composer_client']['removeCandidate'],
			$removeName
		);

		$_SESSION['COMPOSER_OUTPUT'] .= $this->io->getOutput();

		$this->redirect('contao/main.php?do=composer');
	}
}
