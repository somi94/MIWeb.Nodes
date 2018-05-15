<?php
namespace MIWeb\Nodes\Domain\Service;

use MIWeb\Nodes\Domain\Model\Node;
use MIWeb\Nodes\NodeTypes\NodeTypeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Exception;

/**
 * This is the Domain Service which acts as a helper for tasks
 * affecting entities inside the Party context.
 *
 * @Flow\Scope("singleton")
 */
class NodeService {
	/**
	 * @Flow\InjectConfiguration(package="MIWeb.Nodes", path="nodeTypes")
	 * @var array
	 */
	protected $nodeTypes;

	/**
	 * @Flow\InjectConfiguration(package="MIWeb.Nodes", path="nodeEditors")
	 * @var array
	 */
	protected $nodeEditors;

	/**
	 * @var array
	 */
	protected $nodeTypeCache = [];

	/**
	 * @param array $config
	 * @return Node
	 */
	public function createFromConfig($config) {
		$node = new Node();
		$node->setNodeType($config['nodeType']);
		$node->setAttributes(isset($config['attributes']) ? $config['attributes'] : []);

		return $node;
	}

	/**
	 * @param string $nodeTypeIdentifier
	 * @return NodeTypeInterface
	 * @throws Exception
	 */
	public function getNodeType($nodeTypeIdentifier) {
		if(isset($this->nodeTypeCache[$nodeTypeIdentifier])) {
			return $this->nodeTypeCache[$nodeTypeIdentifier];
		}

		$nodeTypeConfig = $this->getNodeTypeConfig($nodeTypeIdentifier);
		if(!isset($nodeTypeConfig['class'])) {
			throw new Exception("Missing class definition for node type \"$nodeTypeIdentifier\".");
		}
		$nodeTypeClassName = $nodeTypeConfig['class'];

		$nodeType = new $nodeTypeClassName($nodeTypeIdentifier, $nodeTypeConfig);
		$this->nodeTypeCache[$nodeTypeIdentifier] = $nodeType;
		return $nodeType;
	}

	/**
	 * @param string $nodeTypeIdentifier
	 * @return array
	 * @throws Exception
	 */
	public function getNodeTypeConfig($nodeTypeIdentifier) {
		if(!isset($this->nodeTypes[$nodeTypeIdentifier])) {
			throw new Exception("Unknown Node Type \"$nodeTypeIdentifier\".");
		}

		return $this->nodeTypes[$nodeTypeIdentifier];
	}

	/**
	 * @param string $nodeEditorIdentifier
	 * @return array
	 * @throws Exception
	 */
	public function getNodeEditorConfig($nodeEditorIdentifier) {
		if(!isset($this->nodeEditors[$nodeEditorIdentifier])) {
			throw new Exception("Unknown Node Editor \"$nodeEditorIdentifier\".");
		}

		return $this->nodeEditors[$nodeEditorIdentifier];
	}
}
