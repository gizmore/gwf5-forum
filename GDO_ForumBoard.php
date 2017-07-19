<?php
/**
 * A selection for a GWF_Category object.
 * @author gizmore
 * @see GWF_Category
 */
final class GDO_ForumBoard extends GDO_Select
{
	use GDO_ObjectTrait;
	
	public function defaultLabel() { return $this->label('board'); }
	
	public function __construct()
	{
		$this->table(GWF_ForumBoard::table());
		$this->emptyChoice('no_parent');
	}
	
	/**
	 * @return GWF_ForumBoard
	 */
	public function getBoard()
	{
		return $this->getGDOValue();
	}
	
	public function withCompletion()
	{
	 	$this->completion(href('Forum', 'BoardCompletion'));
	}
	
	public function renderCell()
	{
		return GWF_Template::modulePHP('Category', 'cell/board.php', ['field'=>$this]);
	}
	
	public function renderChoice()
	{
		return GWF_Template::modulePHP('Category', 'choice/board.php', ['field'=>$this]);
	}

	public function render()
	{
		if ($this->completionURL)
		{
			return GWF_Template::mainPHP('form/object_completion.php', ['field'=>$this]);
		}
		else
		{
			$this->choices($this->boardChoices());
			return GWF_Template::mainPHP('form/select.php', ['field'=>$this]);
		}
	}
	
	public function validate($value)
	{
	    $this->choices($this->boardChoices());
		return parent::validate($value);
	}
	
	public function boardChoices()
	{
		return GWF_ForumBoard::table()->all();
	}
	
}
