<?php

namespace App\DQL;

use Doctrine\ORM\Query\{Lexer, Parser, SqlWalker};
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class RandFunction extends FunctionNode
{
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'RAND()';
    }
}