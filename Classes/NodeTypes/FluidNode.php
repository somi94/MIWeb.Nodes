<?php
namespace MIWeb\Nodes\NodeTypes;


use MIWeb\Nodes\Domain\Model\Node;
use Neos\FluidAdaptor\View\StandaloneView;
use Neos\Utility\ObjectAccess;

class FluidNode extends AbstractNodeType {
	/**
	 * @param Node $node
	 * @return string
	 */
	public function render($node) {
		$templatePathAndFilename = $this->getAttributeValue('templatePathAndFilename', $node);
		$layoutPathAndFilename = $this->getAttributeValue('layoutPathAndFilename', $node);

		return $this->renderView($node, $templatePathAndFilename, $layoutPathAndFilename);
	}

	protected function renderView($node,$template,$layout = null) {
		$options = [
			'templatePathAndFilename' => $template
		];
		if($layout) {
			$options['layoutPathAndFilename'] = $layout;
		}


		$view = StandaloneView::createWithOptions($options);
		$view->assign('node',$node);

		return $view->render();
	}

	/**
	 * @param Iterable<Node> $nodes
	 * @param int $page
	 * @param int $numPages
	 * @return string
	 */
	public function renderList($nodes, $page = 0, $numPages = 0) {
		$template = $this->getAttributeValue('listTemplatePathAndFilename');
		$layout = $this->getAttributeValue('layoutPathAndFilename');

		if(!$template) {
			return parent::renderList($nodes);
		}

		$options = [
			'templatePathAndFilename' => $template
		];
		if($layout) {
			$options['layoutPathAndFilename'] = $layout;
		}

		$view = StandaloneView::createWithOptions($options);
		$view->assign('nodes',$nodes);
		$view->assign('page',$page);
		$view->assign('numPages',$numPages);

		return $view->render();

		/*$layoutPathAndFilename = $this->getAttributeValue('layoutPathAndFilename');

		$out = "";
		foreach($nodes as $node) {
			$templatePathAndFilename = $this->getAttributeValue('templatePathAndFilename', $node);

			$out .= $this->renderView($node,$templatePathAndFilename);
		}

		return $out;*/
	}
}
