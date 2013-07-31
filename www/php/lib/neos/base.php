<?php
namespace Lib;

/**
 * Description of base
 *
 * @author Paulo
 */

abstract class Base {

	/**
	 * referencia estática a própria classe!
	 * Todas as classes que "extends" essa BASE armazenam sua instância singleton neste array.
	 */
	static $THIS = array();


	/**
	 * Construtor singleton da própria classe.
	 * Acessa o método estático para criar uma instância da classe automáticamente.
	 *
	 * @param string $class Classe invocada.
	 * @return object this instance
	*/
	final public static function this(){
		$class = get_called_class();
		if (!isset(static::$THIS[$class])) static::$THIS[$class] = new static;
		return static::$THIS[$class];
	}

	/**
	 * Simples setter!.
	 * Acessa e modifica um atributo privado ou público da classe.
	 *
	 * @param string $var nome do atributo.
	 * @param mixed $val novo valor do atributo.
	 * @return mixed|null retorna o valor modificado ou null se o atributo não for acessível (não existir).
	*/
	static function set($var, $val){
		return self::this()->$var = $val;
	}

	/**
	 * Simples getter!.
	 * Retorna o valor de um atributo privado ou público da classe.
	 *
	 * @param string $var nome do atributo.
	 * @return mixed|null retorna o valor ou null se o atributo não for acessível (não existir).
	*/
	static function get($var = null){
		if($var == null) return self::this();//retorna TODOS os argumentos da classe
		if(isset(self::this()->$var)) return self::this()->$var;
		return null;
	}

	/*
	 * Dispara o sistema de ERRORs
	 *
	 * @param $msg String Mensagem de erro a ser exibida
	 * @param $cod Number (se existir) Código da ajuda para o erro
	 *
	 * @return void 	Gera um erro no sistema!
	 */
	 static function _error($msg, $cod = 0, $class = null){
		\Lib\Error\Error::this()->codigo = $cod;
		\Lib\Error\Error::this()->classPath = ($class != null) ? $class : get_called_class();
		trigger_error($msg);
	 }

}