<?php

class ShipmentPiece extends Piece
{
    protected $required = array(
        'PieceID' => null,
        'PackageType' => null,
        'Weight' => null,
        'Width' => null,
        'Height' => null,
        'Depth' => null
    );
}
