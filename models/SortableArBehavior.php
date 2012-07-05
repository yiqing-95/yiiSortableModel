<?php
/**
 * User: yiqing
 * Date: 12-7-5
 * Time: 下午5:53
 * To change this template use File | Settings | File Templates.
 *------------------------------------------------------------
 *------------------------------------------------------------
 */
class SortableArBehavior extends CActiveRecordBehavior
{

    public $orderField = 'order';


    protected function beforeSave()
    {
        $model = $this->getOwner();
        if ($model->isNewRecord) {
            $model2 = call_user_func(array(get_class($model), 'model'));
            $last_record = $model2->find(array(
                'order' => '`'.$this->orderField.'` DESC',
                'limit' => 1
            ));
            if ($last_record) {
                $model->{$this->orderField} = $last_record->{$this->orderField} + 1;
            } else {
                $model->{$this->orderField} = 1;
            }
        }

    }

    protected function afterDelete()
    {
        $model= $this->getOwner();
        $model2 = call_user_func(array(get_class($model), 'model'));
        $following_records = $model2->findAll(array(
            'order' => '`'.$this->orderField.'` ASC',
            'condition' => '`'.$this->orderField.'` > '.$model->{$this->orderField},
        ));
        foreach ($following_records as $record) {
            $record->{$this->orderField}--;
            $record->update();
        }
    }
}