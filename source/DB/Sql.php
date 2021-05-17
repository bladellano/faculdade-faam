<?php

namespace Source\DB;

class Sql
{

	const HOSTNAME = DB_SITE['host'];
	const USERNAME = DB_SITE['username'];
	const PASSWORD = DB_SITE['passwd'];
	const DBNAME = DB_SITE['dbname'];
	const OPTIONS = DB_SITE['options'];

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=" . Sql::DBNAME . ";host=" . Sql::HOSTNAME,
			Sql::USERNAME,
			Sql::PASSWORD,
			Sql::OPTIONS
		);
	}

	private function setParams($statement, $parameters = array())
	{
		foreach ($parameters as $key => $value) {
			$this->bindParam($statement, $key, $value);
		}
	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);
	}

	/**
	 * Função que realiza update no banco sem usar procedures
	 * @param [type] $entity - nome da tabela
	 * @param array $params - array de chave/valores
	 * @return void
	 */
	public function update($entity, $params = array()): void
	{
		foreach (array_keys($params) as $key)
			$tplInputs[] = "{$key} = :{$key}";

		$tplInputs = implode(", ", $tplInputs);

		$rawQuery = "UPDATE {$entity} SET {$tplInputs} WHERE id = :id";
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);
		$stmt->execute();
	}


	/**
	 * Função que insere no banco sem usar procedures
	 * @param [type] $entity - nome da tabela
	 * @param array $params - array de chave/valores
	 * @return void
	 */
	public function insert($entity, $params = array()): string
	{
		$tplInputs = ":" . implode(", :", array_keys($params));
		$namesInputs = implode(", ", array_keys($params));

		$rawQuery = "INSERT INTO {$entity} ({$namesInputs}) VALUES ({$tplInputs})";
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);

		try {
			$stmt->execute();
		} catch (\PDOException $e) {
			die($e->getMessage());
		}

		return $this->conn->lastInsertId();
	}


	public function query($rawQuery, $params = array())
	{
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);
		$stmt->execute();
	}

	public function select($rawQuery, $params = array()): array
	{
		$stmt = $this->conn->prepare($rawQuery);
		$this->setParams($stmt, $params);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}
