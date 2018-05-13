<?php
namespace MIWeb\Nodes\Controller;

use MIWeb\Nodes\Domain\Model\Node;
use MIWeb\Nodes\Domain\Repository\NodeRepository;
use MIWeb\Nodes\Domain\Service\NodeService;
use MIWeb\Nodes\NodeTypes\NodeTypeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;
use Neos\Flow\Mvc\Controller\ActionController;

class NodeController extends ActionController {
	/**
	 * @var NodeRepository
	 * @Flow\Inject
	 */
	protected $nodeRepository;

	/**
	 * @var NodeService
	 * @Flow\Inject
	 */
	protected $nodeService;

	/**
	 * @Flow\InjectConfiguration(package="MIWeb.Nodes")
	 * @var array
	 */
	protected $settings;

	/**
	 * @param string $node
	 * @return string
	 * @throws Exception
	 */
	public function indexAction($node = null) {
		if(!$node) {
			throw new Exception("No node given.");
		}

		$node = $this->nodeRepository->findByIdentifier($node);
		if($node == null) {
			if(isset($this->settings['fallbackNode'])) {
				$node = $this->nodeService->createFromConfig($this->settings['fallbackNode']);
			} else {
				throw new Exception("Node not found.");
			}
		}

		$nodeType = $node->getNodeType();

		return $nodeType->render($node);
	}

	/**
	 * @param string $attribute
	 * @param string $value
	 * @param string|null $nodeType
	 * @return string
	 * @throws Exception
	 */
	public function lookupAction($attribute, $value, $nodeType = null) {
		if(!$attribute) {
			throw new Exception("No search attribute given.");
		}
		if(!$value) {
			throw new Exception("No search value given.");
		}

		$node = $this->nodeRepository->findByAttribute($attribute, $value, $nodeType);
		if($node == null) {
			if(isset($this->settings['fallbackNode'])) {
				$node = $this->nodeService->createFromConfig($this->settings['fallbackNode']);
			} else {
				throw new Exception("Node not found.");
			}
		}

		$nodeType = $node->getNodeType();

		return $nodeType->render($node);
	}

	/**
	 * @param string $nodeType
	 * @param int page
	 * @return string
	 * @throws Exception
	 */
	public function listAction($nodeType,$page = 0) {
		$nodeType = $this->nodeService->getNodeType($nodeType);
		$listLimit = isset($nodeType->getConfig()['listLimit']) ? $nodeType->getConfig()['listLimit'] : 0;

		$nodes = $this->nodeRepository->findByNodeType($nodeType,($page ? $page - 1 : 0) * $listLimit,$listLimit);

		$numPages = floor(($this->nodeRepository->countByNodeType($nodeType) - 1) / $listLimit) + 1;

		return $nodeType->renderList($nodes, $page ? $page - 1 : 0, $numPages);
	}
}
