<?php
namespace MIWeb\Nodes\Domain\Repository;

use MIWeb\Nodes\NodeTypes\NodeTypeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;
use MIWeb\Nodes\Domain\Model\Node;

/**
 * Repository for nodes
 *
 * @Flow\Scope("singleton")
 */
class NodeRepository extends Repository {
	/**
	 * @return \Neos\Flow\Persistence\QueryResultInterface<Node>
	 */
	public function findAllActive() {
		$query = $this->createQuery();
		$query->matching($query->equals('deleted',null));
		return $query->execute();
	}

	/**
	 * @param string|NodeTypeInterface $nodeType
	 * @return \Neos\Flow\Persistence\QueryResultInterface<Node>
	 */
	public function findByNodeType($nodeType, $offset = 0, $limit = 0) {
		if($nodeType instanceof NodeTypeInterface) {
			$nodeType = $nodeType->getIdentifier();
		}
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('nodeType', $nodeType),
			$query->equals('deleted',null)
		));
		if($offset) {
			$query->setOffset($offset);
		}
		if($limit) {
			$query->setLimit($limit);
		}
		$query->setOrderings(['created' => 'desc']);
		return $query->execute();
	}

	/**
	 * @param string|NodeTypeInterface $nodeType
	 * @return int
	 */
	public function countByNodeType($nodeType) {
		if($nodeType instanceof NodeTypeInterface) {
			$nodeType = $nodeType->getIdentifier();
		}
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('nodeType', $nodeType),
			$query->equals('deleted',null)
		));
		return $query->count();
	}

	/**
	 * @param string $identifier
	 * @return Node
	 */
	public function findByIdentifier($identifier) {
		$query = $this->createQuery();
		return $query->matching($query->equals('identifier', $identifier))->execute()->getFirst();
	}

	/**
	 * @param string $attribute
	 * @param mixed $value
	 * @param string|NodeTypeInterface $nodeType
	 * @return Node
	 */
	public function findByAttribute($attribute,$value,$nodeType = null) {
		/*$query = $this->createQuery();
		return $query->matching($query->equals('attributes.' . $attribute, $value))->execute()->getFirst();*/

		$nodes = [];
		if($nodeType) {
			$nodes = $this->findByNodeType($nodeType);
		} else {
			$nodes = $this->findAllActive();
		}

		foreach($nodes as $node) {
			if($node->getAttribute($attribute) == $value) {
				return $node;
			}
		}

		return null;
	}
}
