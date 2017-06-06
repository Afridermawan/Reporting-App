<?php

namespace App\Models;

class UserGroupModel extends BaseModel
{
	protected $table = 'user_group';
	protected $column = ['group_id', 'user_id', 'status'];

	//Set user as member group
	function add(array $data)
	{
		$data = [
			'group_id' 	=> 	$data['group_id'],
			'user_id'	=>	$data['user_id'],
			'status'	=>	$data['status'],
		];
		$this->createData($data);

		return $this->db->lastInsertId();
	}

	//Find all user user by column
	public function findUsers($column, $value)
    {
        $param = ':'.$column;
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
            ->from($this->table)
            ->setParameter($param, $value)
            ->where($column . ' = '. $param);
        $result = $qb->execute();
        return $result->fetchAll();
    }

	//Find one user user by column
	public function findUser($column1, $val1, $column2, $val2)
	{
		$param1 = ':'.$column1;
		$param2 = ':'.$column2;
		$qb = $this->db->createQueryBuilder();
		$qb->select('*')
			->from($this->table)
			->setParameter($param1, $val1)
			->setParameter($param2, $val2)
			->where($column1 . ' = '. $param1 .'&&'. $column2 . ' = '. $param2);
		$result = $qb->execute();
		return $result->fetch();
	}

	//Set user in group as PIC
	public function setPic($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 1)
		   ->where('id = ' . $id)
		   ->execute();
	}

	//Set user in group as member
	public function setUser($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 0)
 	   	   ->where('id = ' . $id)
		   ->execute();
	}

	// Get all user in group by group id
	public function findAll($groupId)
    {
        $qb = $this->db->createQueryBuilder();

        $this->query = $qb->select('users.*', 'user_group.*')
         	 ->from('users', 'users')
        	 ->join('users', $this->table, 'user_group', 'users.id = user_group.user_id')
        	 ->where('user_group.group_id = :id')
        	 ->setParameter(':id', $groupId);

        return $this;
    }

	//Get all users are not registered in group
	public function notMember($groupId)
	{
		$qb = $this->db->createQueryBuilder();

		$query1 = $qb->select('user_id')
					 ->from($this->table)
					 ->where('group_id =' . $groupId)
					 ->execute();

		$qb1 = $this->db->createQueryBuilder();

		$this->query = $qb1->select('u.*')
			 ->from($this->table, 'ug')
	 		 ->join('ug', 'users', 'u', $qb1->expr()->notIn('u.id', $query1))
			 ->where('deleted = 0')
			 ->andWhere('u.status = 0')
			 ->groupBy('u.id');

		return $this;
	}

	//Get user by user id & group id
	public function getUser($groupId, $userId)
	{
		$qb = $this->db->createQueryBuilder();
		$parameters = [
			':user_id' => $userId,
			':group_id' => $groupId
		];
		$qb->select('users.name', 'users.email', 'users.gender', 'users.address', 'users.image','users.phone')
		   ->from('users', 'users')
		   ->join('users', $this->table, 'ug', 'ug.user_id = users.id')
		   ->where('ug.user_id = :user_id')
		   ->andWhere('ug.group_id = :group_id')
		   ->setParameters($parameters);

		$result = $qb->execute();
		return $result->fetchAll();
	}

	// Get all groups by user id
	public function findAllGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('groups.*', 'user_group.*')
			 ->from('groups', 'groups')
			 ->join('groups', $this->table, 'user_group', 'groups.id = user_group.group_id')
			 ->where('user_group.user_id = :id')
			 ->setParameter(':id', $userId);

			 $result = $qb->execute();
 			return $result->fetchAll();
	}

	public function generalGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('groups.*', 'user_group.*')
			 ->from('groups', 'groups')
			 ->join('groups', $this->table, 'user_group', 'groups.id = user_group.group_id')
			 ->where('user_group.user_id = :id')
			 ->andWhere('user_group.status = 0')
			 ->setParameter(':id', $userId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function picGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('groups.*', 'user_group.*')
			 ->from('groups', 'groups')
			 ->join('groups', $this->table, 'user_group', 'groups.id = user_group.group_id')
			 ->where('user_group.user_id = :id')
			 ->andWhere('user_group.status = 1')
			 ->setParameter(':id', $userId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}
}

?>
