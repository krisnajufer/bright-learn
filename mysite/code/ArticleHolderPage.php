<?php

class ArticleHolderPage extends Page
{
    private static $has_many = array(
        'Categories' => 'ArticleCategory'
    );


    private static $allowed_children = array(
        'ArticlePage'
    );


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Categories', GridField::create(
            'Categories',
            'Article categories',
            $this->Categories(),
            GridFieldConfig_RecordEditor::create()
        ));

        return $fields;
    }

    public function Regions()
    {
        try {
            $page = RegionPage::get()->first();

            if ($page) {
                return $page->Regions();
            }
        } catch (\Exception $ex) {
            echo $ex->getMessage();
            die;
        }
    }

    public function ArchiveDates()
    {
        try {
            $list = ArrayList::create();
            $stage = Versioned::current_stage();

            $query = new SQLQuery(array());
            $query->selectField("DATE_FORMAT(`Date`,'%Y_%M_%m')", "DateString")
                ->setFrom("ArticlePage_{$stage}")
                ->setOrderBy("DateString", "ASC")
                ->setDistinct(true);

            $result = $query->execute();

            if ($result) {
                while ($record = $result->nextRecord()) {
                    list($year, $monthName, $monthNumber) = explode('_', $record['DateString']);

                    $list->push(ArrayData::create(array(
                        'Year' => $year,
                        'MonthName' => $monthName,
                        'MonthNumber' => $monthNumber,
                        'Link' => $this->Link("date/$year/$monthNumber"),
                        'ArticleCount' => ArticlePage::get()->where("
							DATE_FORMAT(`Date`,'%Y%m') = '{$year}{$monthNumber}'
							AND ParentID = {$this->ID}
						")->count()
                    )));
                }
            }

            return $list;
        } catch (Exception $ex) {
            //throw $th;
            echo $ex->getMessage();
        }
    }
}
