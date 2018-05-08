<?php
namespace MIWeb\Nodes\ViewHelpers;

use MIWeb\Nodes\Domain\Model\Node;
use MIWeb\Nodes\Domain\Service\NodeService;
use MIWeb\Nodes\NodeTypes\NodeTypeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use Neos\FluidAdaptor\ViewHelpers\Form\AbstractFormViewHelper;
use Neos\FluidAdaptor\ViewHelpers\FormViewHelper;
use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;

class NodeFormViewHelper extends FormViewHelper {
	/**
	 * @var NodeService
	 * @Flow\Inject
	 */
	protected $nodeService;

	/**
	 * @throws \Neos\FluidAdaptor\Core\ViewHelper\Exception
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument("node", Node::class, "the node to build a form for", true);
		$this->registerArgument("identifier", "string", "the identifier to use", false);
		$this->registerArgument("submit", "string", "the submit button value", false, "Submit");
	}

	public function initialize() {
		$this->arguments['object'] = $this->arguments['node'];
		$this->arguments['objectName'] = isset($this->arguments['name']) && $this->arguments['name'] ? $this->arguments['name'] : 'node';

		$class = isset($this->arguments['class']) ? $this->arguments['class'] : '';
		$this->arguments['class'] = $class . ' node-form node-' . strtolower($this->arguments['node']->getIdentifier());

		return parent::initialize();
	}

	public function renderChildren() {
		/**
		 * @var Node $node
		 */
		$node = $this->arguments['node'];
		$nodeType = $node->getNodeType();
		$nodeTypeConfig = $nodeType->getConfig();

		if(!isset($nodeTypeConfig['editors'])) {
			return '';
		}

		$contents = '';

		if(isset($this->arguments['identifier'])) {
			if($this->arguments['identifier']) {
				$contents .= $this->renderProperty(
					'identifier',
					null,
					'Hidden'
				);
			}
		} else {
			$contents .= $this->renderProperty(
				'identifier',
				'Identifier',
				'Textfield'
			);
		}

		foreach($nodeTypeConfig['editors'] as $attribute => $editorConfig) {
			if(!isset($editorConfig['editor'])) {
				continue;
			}

			$contents .= $this->renderProperty(
				'attributes.' . $attribute,
				isset($editorConfig['label']) ? $editorConfig['label'] : null,
				$editorConfig['editor'],
				'attribute-' . $attribute
			);

			/*$viewhelper->setRenderingContext($this->renderingContext);
			$viewhelper->setArguments([
				'value' => $node->getAttribute($attribute),
				'property' => 'attributes.' . $attribute
			]);
			$contents .= $viewhelper->initializeArgumentsAndRender();*/
		}

		if($this->arguments['submit']) {
			$contents .= '<p><input class="btn btn-primary btn-sm btn-flat" type="submit" value="' . $this->arguments['submit'] . '" /></p>';
		}

		return $contents . parent::renderChildren();
	}

	protected function renderProperty($property,$label = null,$editor = 'Textfield',$class = '') {
		$viewhelper = $editor;
		$viewhelper = "Neos\\FluidAdaptor\\ViewHelpers\\Form\\" . $viewhelper . "ViewHelper";
		if(!class_exists($viewhelper)) {
			return "<p><i>ViewHelper not found: '$viewhelper'</i></p>";
		}

		$contents = "<p>";

		if($label) {
			$contents .= "<label>" . $label . "</label><br>";
		}

		$contents .= $this->renderingContext->getViewHelperInvoker()->invoke($viewhelper, [
			//'value' => $node->getAttribute($attribute),
			//'name' => $this->arguments['objectName'] . "[attributes][$attribute]"
			'property' => $property,
			'class' => $class . ' editor-' . strtolower($editor)
		], $this->renderingContext);

		$contents .= "</p>";

		return $contents;
	}
}
