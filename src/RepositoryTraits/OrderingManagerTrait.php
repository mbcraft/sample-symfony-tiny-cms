<?php

namespace App\RepositoryTraits;

use Doctrine\ORM\NoResultException;

trait OrderingManagerTrait {

    private static $lastOrderingCache = [];

    public function getOrderedFullList(array $criteria) {

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX();

        foreach ($criteria as $key => $value) {
            if ($value === null)
                $and_cond->add($qb->expr()->isNull($key));
            else
                $and_cond->add($qb->expr()->eq($key,$value));
        }

        if ($and_cond->count() > 0)
            $qb->where($and_cond);

        $result = $qb->orderBy('el.order_val','ASC')->getQuery()->getResult();

        $this->mark_first_and_last($result);

        return $result;

    }

    public function move_to_previous($el) {

        $this->checkRequiredOrderingConstants();

        $previous = $this->findPreviousElement($el);

        if ($previous) {
            $this->exchange_order($previous,$el);
        }

    }

    public function move_to_next($el) {

        $this->checkRequiredOrderingConstants();
        
        $next = $this->findNextElement($el);

        if ($next) {
            $this->exchange_order($el,$next);
        }

    }

    public function move_to_first($el) {

        $this->checkRequiredOrderingConstants();

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX($qb->expr()->notIn('el.id',[$el->getId()]));

        //$this->pushNotSoftDeletedCondition($el,$and_cond);

        $this->addOrderingGroupToCondition($and_cond,$el);

        $all_elements = $qb->where($and_cond)->orderBy('el.'.static::MY_ORDER_FIELD,'ASC')->getQuery()->getResult();

        $final_array = [$el];

        foreach ($all_elements as $other_el) {
            $final_array [] = $other_el;
        }

        $this->reorder_all($final_array);
    }

    public function move_to_last($el) {

        $this->checkRequiredOrderingConstants();

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX($qb->expr()->notIn('el.id',[$el->getId()]));

        //$this->pushNotSoftDeletedCondition($el,$and_cond);

        $this->addOrderingGroupToCondition($and_cond,$el);

        $all_elements = $qb->where($and_cond)->orderBy('el.'.static::MY_ORDER_FIELD,'ASC')->getQuery()->getResult();

        $final_array = [];

        foreach ($all_elements as $other_el) {
            $final_array [] = $other_el;
        }

        $final_array[] = $el;

        $this->reorder_all($final_array);

    }

    public function mark_first_and_last($collection) {

        $this->checkRequiredOrderingConstants();

        
        foreach ($collection as $el) {
            if ($el->{static::MY_ORDER_GETTER}() == 1) $el->markAsFirst();
        
            $lastValue = $this->getOrderColumnLast($el);

            if ($el->{static::MY_ORDER_GETTER}() == $lastValue) $el->markAsLast();
        }
        
    }

    private function getUsedTraits($classInstance) {
    $parentClasses = class_parents($classInstance);
    $traits = class_uses($classInstance);
    
    foreach ($parentClasses as $parentClass) {
        $traits = array_merge($traits, class_uses($parentClass));
    }
    
    return $traits;
}

    // end of public methods 

    private function checkRequiredOrderingConstants() {

		$entity_clazz = $this->getEntityName();

		$clazz_traits = $this->getUsedTraits($entity_clazz);

		if (!in_array("App\EntityTraits\OrderableTrait",$clazz_traits)) throw new \Exception("The entity class must use the App\EntityTraits\Orderable trait!");

        if (!defined('static::MY_ORDER_FIELD')) throw new \Exception("Constant of repository MY_ORDER_FIELD is not defined!");
        if (!defined('static::MY_ORDER_GETTER')) throw new \Exception("Constant of repository MY_ORDER_GETTER is not defined!");
        if (!defined('static::MY_ORDER_SETTER')) throw new \Exception("Constant of repository MY_ORDER_SETTER is not defined!");
        if (!defined('static::MY_ORDER_GROUP_FIELDS')) throw new \Exception("Constant of repository MY_ORDER_GROUP_FIELDS is not defined!");
        if (!defined('static::MY_ORDER_GROUP_GETTERS')) throw new \Exception("Constant of repository MY_ORDER_GROUP_GETTERS is not defined!");

    }

	private function reorder_all(array $data) {

        $order_val = 1;

        foreach ($data as $el) {
            $el->{static::MY_ORDER_SETTER}($order_val);
            $order_val++;
        }

        $this->getEntityManager()->flush();

    }

    private function exchange_order($el1,$el2) {

        $tmp1 = $el1->{static::MY_ORDER_GETTER}();
        $tmp2 = $el2->{static::MY_ORDER_GETTER}();

        $el1->{static::MY_ORDER_SETTER}($tmp2);
        $el2->{static::MY_ORDER_SETTER}($tmp1);

        $this->getEntityManager()->flush();

    }

    private function addOrderingGroupToCondition($cond,$element) {
        
        $qb = $this->createQueryBuilder('el');

        $getters = static::MY_ORDER_GROUP_GETTERS;

        $fields = static::MY_ORDER_GROUP_FIELDS;

        $i = 0;

        foreach ($getters as $get_method) {

            $value = $element->{$get_method}();

            if (is_object($value))
                $value_id = $value->getId();
            else
                $value_id = $value;

            if ($value === null)
                $cond->add($qb->expr()->isNull('el.'.$fields[$i]));
            else
                $cond->add($qb->expr()->eq('el.'.$fields[$i],$value_id));

            $i++;

        }
    }

    private function findPreviousElement($el) {

        $order_val = $el->{static::MY_ORDER_GETTER}();

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX($qb->expr()->lt('el.'.static::MY_ORDER_FIELD,$order_val));

        //$this->pushNotSoftDeletedCondition($el,$cond);

        $this->addOrderingGroupToCondition($and_cond,$el);

        try {
        $result = $qb->where($and_cond)->orderBy('el.'.static::MY_ORDER_FIELD,'DESC')->setMaxResults(1)->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
        return $result;
    }

    private function findNextElement($el) {

        $order_val = $el->getOrderVal();

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX($qb->expr()->gt('el.'.static::MY_ORDER_FIELD,$order_val));

        //$this->pushNotSoftDeletedCondition($el,$cond);

        $this->addOrderingGroupToCondition($and_cond,$el);

        try {
            $result = $qb->where($and_cond)->orderBy('el.'.static::MY_ORDER_FIELD,'ASC')->setMaxResults(1)->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            return null;
        }
        return $result;
    }

    private function getOrderColumnLastCacheName($el) {

    	$fields = static::MY_ORDER_GROUP_FIELDS;

    	$name = "";

    	$i = 0;

        foreach (static::MY_ORDER_GROUP_GETTERS as $getter_name) {
            
        	$name .= $fields[$i];

            $value = $el->{$getter_name}();

            if ($value===null) $name.= "_NULL";
            else {
                if (is_object($value))
                    $name .= "_".$value->getId();
                else
                    $name .= "_".$value;
            }

            $i++;

            $name .= "__";
        }

        return $name;
    }

    public function setOrderingForEntity($el) {

        $value = $this->getOrderColumnLast($el);

        $el->{static::MY_ORDER_SETTER}($value + 1);

    }

    private function getOrderColumnLast($el) {

        $last_cache_name = $this->getOrderColumnLastCacheName($el);

        if (isset(self::$lastOrderingCache[$last_cache_name])) return self::$lastOrderingCache[$last_cache_name];

        $value = $this->calculateOrderColumnLast($el);

        self::$lastOrderingCache[$last_cache_name] = $value;

        return $value; 

    }

    private function calculateOrderColumnLast($el) {

        $qb = $this->createQueryBuilder('el');

        $and_cond = $qb->expr()->andX();
        
        //$this->pushNotSoftDeletedCondition($el,$and_cond);

        $this->addOrderingGroupToCondition($and_cond,$el);

        $qb->select('count(el)');

        if ($and_cond->count() > 0) $qb->where($and_cond);

        $total = $qb->getQuery()->getSingleScalarResult();

        return $total;

    }

  
}