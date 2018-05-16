<?php
namespace MIWeb\Nodes\Domain\Model;

/*
 * This file is part of the Neos.Party package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MIWeb\Nodes\Domain\Service\NodeService;
use MIWeb\Nodes\NodeTypes\NodeTypeInterface;
use Neos\ContentRepository\Domain\Model\NodeType;
use Neos\Flow\Annotations as Flow;

/**
 * A node
 *
 * @Flow\Entity
 */
class Node {
	/**
	 * @var string
	 * @Flow\Identity
	 */
	protected $identifier;

	/**
	 * @var string
	 */
	protected $nodeType;

	/**
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $created = null;

	/**
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $modified = null;

	/**
	 * @var \DateTime
	 * @ORM\Column(nullable=true)
	 */
	protected $deleted = null;

	/**
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * @var NodeService
	 * @Flow\Inject
	 */
	protected $nodeService;

	public function __construct() {
		$this->created = new \DateTime('now');
	}

	/**
	 * @return string
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * @param string $identifier
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name) {
		return isset($this->attributes[$name]);
	}

	/**
	 * @param string $name
	 * @return string|null
	 */
	public function getAttribute($name) {
		/*if(!$this->attributesInitialized) {
			$this->attributes = array_merge($this->nodeService->getNodeTypeConfig($this->nodeType)['defaults'], $this->attributes);
			$this->attributesInitialized = true;
		}*/

		return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		/*if(!$this->attributesInitialized) {
			$this->attributes = array_merge($this->nodeService->getNodeTypeConfig($this->nodeType)['defaults'], $this->attributes);
			$this->attributesInitialized = true;
		}*/

		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}

	/**
	 * @param string $attribute
	 * @param string $value
	 */
	public function setAttribute($attribute,$value) {
		$this->attributes[$attribute] = $value;
	}

	/**
	 * @return NodeTypeInterface
	 */
	public function getNodeType() {
		return $this->nodeService->getNodeType($this->nodeType);
	}

	/**
	 * @param string|NodeTypeInterface $nodeType
	 */
	public function setNodeType($nodeType) {
		if(is_string($nodeType)) {
			$this->nodeType = $nodeType;
		} else if($nodeType instanceof NodeTypeInterface) {
			$this->nodeType = $nodeType->getIdentifier();
		}
	}

	/**
	 * @return array
	 */
	public function getMetaData() {
		$meta = [];
		foreach($this->getNodeType()->getMetaAttributes() as $attribute) {
			$meta[$attribute] = $this->getAttribute($attribute);
		}
		return $meta;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return void
	 */
	public function setModified() {
		$this->modified = new \DateTime('now');
		if($this->created == null) {
			$this->created = $this->modified;
		}
	}

	/**
	 * @return void
	 */
	public function setDeleted() {
		$this->deleted = new \DateTime('now');
	}

	/**
	 * @return void
	 */
	public function setUndeleted() {
		$this->deleted = null;
	}
}
