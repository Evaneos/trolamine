<?php
namespace Trolamine\Core\Operation;

use Trolamine\Core\Authentication\Authentication;

interface Operation{
    
    /**
     * Sets the {@link Authentication} used for evaluating the expressions
     * 
     * @param Authentication $authentication the {@link Authentication} for evaluating the expressions
     */
    function setAuthentication(Authentication $authentication);
    
    /**
     * Gets the {@link Authentication} used for evaluating the expressions
     *
     * @return Authentication the {@link Authentication} for evaluating the expressions
     */
    function getAuthentication();
}
