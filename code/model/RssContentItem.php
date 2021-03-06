<?php
/**
 * An RSS entry from an external RSS feed.
 *
 * @package silverstripe-rssconnector
 */
class RssContentItem extends ExternalContentItem
{

    protected $item;
    protected $categories;

    /**
     * @param RssContentSource $source
     * @param SimplePie_Item $item
     */
    public function __construct($source = null, $item = null)
    {
        if (is_object($item)) {
            $this->item = $item;
            $item = $item->get_id();
        }

        parent::__construct($source, $item);
    }

    public function init()
    {
        $this->Title     = $this->item->get_title();
        $this->Link      = $this->item->get_link();
        $this->Date      = $this->item->get_date('Y-m-d H:i:s');
        $this->Content   = $this->item->get_content();

        if ($author = $this->item->get_author()) {
            $this->AuthorName  = $author->get_name();
            $this->AuthorEmail = $author->get_email();
            $this->AuthorLink  = $author->get_link();
        }

        $this->categories = new ArrayList();
        $categories = @$this->item->get_categories();

        if ($categories) {
            foreach ($categories as $category) {
                $this->categories->push(new ArrayData(array(
                'Label'  => $category->get_label(),
                'Term'   => $category->get_term(),
                'Scheme' => $category->get_scheme()
            )));
            }
        }

        $this->Latitude  = $this->item->get_latitude();
        $this->Longitude = $this->item->get_longitude();
        
        unset($this->item);
    }

    public function numChildren()
    {
        return 0;
    }

    public function stageChildren($showAll = false)
    {
        return new ArrayList();
    }

    public function getType()
    {
        return 'file';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        
//		$categories = GridField::create();

//		$categories = new TableListField('Categories', false, array(
//			'Label'  => 'Label',
//			'Term'   => 'Term',
//			'Scheme' => 'Scheme'
//		));
//		$categories->setCustomSourceItems($this->categories);
//
//		$fields->addFieldsToTab('Root.Details', array(
//			new HeaderField('CategoriesHeader', 'Categories', 4),
//			$categories->performReadonlyTransformation()
//		));

        $fields->addFieldsToTab('Root.Location', array(
            new ReadonlyField('Latitude', null, $this->Latitude),
            new ReadonlyField('Longitude', null, $this->Longitude)
        ));

        $fields->addFieldToTab('Root.Behaviour', new ReadonlyField(
            'ShowInMenus', null, $this->ShowInMenus
        ));

        return $fields;
    }

    public function getGuid()
    {
        return $this->externalId;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function canImport()
    {
        return false;
    }
}
