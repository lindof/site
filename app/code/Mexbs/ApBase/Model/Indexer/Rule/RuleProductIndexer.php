<?php
namespace Mexbs\ApBase\Model\Indexer\Rule;

use Mexbs\ApBase\Model\Indexer\AbstractIndexer;

class RuleProductIndexer extends AbstractIndexer
{

    protected function doExecuteList($ids)
    {
        $this->indexBuilder->reindexByProductIdsRuleIds(null, $ids);
    }

    protected function doExecuteRow($id)
    {
        $this->indexBuilder->reindexByProductIdsRuleIds(null, [$id]);
    }

    public function getIdentities()
    {
        return [];
    }
}
