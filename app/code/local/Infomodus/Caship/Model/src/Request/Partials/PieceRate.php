<?php
class PieceRate extends RequestPartial
{
    protected $required = array(
        'PieceID' => null,
        'PackageTypeCode' => null,
        'Height' => null,
        'Depth' => null,
        'Width' => null,
        'Weight' => null,
    );

    /**
     * @param integer $pieceId Piece sequence number
     */
    public function setPieceId($pieceId)
    {
        $this->required['PieceID'] = $pieceId;

        return $this;
    }

    /**
     * @param string $height
     */
    public function setHeight($height)
    {
        $this->required['Height'] = $height;

        return $this;
    }

    /**
     * @param string $depth
     */
    public function setDepth($depth)
    {
        $this->required['Depth'] = $depth;

        return $this;
    }

    /**
     * @param string $width
     */
    public function setWidth($width)
    {
        $this->required['Width'] = $width;

        return $this;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->required['Weight'] = $weight;

        return $this;
    }

    public function setPackageTypeCode($packageType)
    {
        $this->required['PackageTypeCode'] = $packageType;

        return $this;
    }
}
