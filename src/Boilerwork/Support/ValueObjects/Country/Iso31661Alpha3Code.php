<?php

/**
 * This file is part of the ValueObject package.
 *
 * (c) Lorenzo Marzullo <marzullo.lorenzo@gmail.com>
 */

namespace Boilerwork\Support\ValueObjects\Country;

use Boilerwork\Foundation\ValueObjects\ValueObject;

/**
 * Class Iso31661Alpha3Code.
 *
 * @package ValueObject
 * @author  Lorenzo Marzullo <marzullo.lorenzo@gmail.com>
 * @link    http://github.com/lorenzomar/valueobject
 *
 */
class Iso31661Alpha3Code extends ValueObject
{
    const AND_ = 'AND_'; // AND is a PHP reserved keyword
    const ARE  = 'ARE';
    const AFG  = 'AFG';
    const ATG  = 'ATG';
    const AIA  = 'AIA';
    const ALB  = 'ALB';
    const ARM_ = 'ARM';
    const AGO  = 'AGO';
    const ATA  = 'ATA';
    const ARG  = 'ARG';
    const ASM  = 'ASM';
    const AUT  = 'AUT';
    const AUS  = 'AUS';
    const ABW  = 'ABW';
    const ALA  = 'ALA';
    const AZE  = 'AZE';
    const BIH  = 'BIH';
    const BRB  = 'BRB';
    const BGD  = 'BGD';
    const BEL  = 'BEL';
    const BFA  = 'BFA';
    const BGR  = 'BGR';
    const BHR  = 'BHR';
    const BDI  = 'BDI';
    const BEN  = 'BEN';
    const BLM  = 'BLM';
    const BMU  = 'BMU';
    const BRN  = 'BRN';
    const BOL  = 'BOL';
    const BES  = 'BES';
    const BRA  = 'BRA';
    const BHS  = 'BHS';
    const BTN  = 'BTN';
    const BVT  = 'BVT';
    const BWA  = 'BWA';
    const BLR  = 'BLR';
    const BLZ  = 'BLZ';
    const CAN  = 'CAN';
    const CCK  = 'CCK';
    const COD  = 'COD';
    const CAF  = 'CAF';
    const COG  = 'COG';
    const CHE  = 'CHE';
    const CIV  = 'CIV';
    const COK  = 'COK';
    const CHL  = 'CHL';
    const CMR  = 'CMR';
    const CHN  = 'CHN';
    const COL  = 'COL';
    const CRI  = 'CRI';
    const CUB  = 'CUB';
    const CPV  = 'CPV';
    const CUW  = 'CUW';
    const CXR  = 'CXR';
    const CYP  = 'CYP';
    const CZE  = 'CZE';
    const DEU  = 'DEU';
    const DJI  = 'DJI';
    const DNK  = 'DNK';
    const DMA  = 'DMA';
    const DOM  = 'DOM';
    const DZA  = 'DZA';
    const ECU  = 'ECU';
    const EST  = 'EST';
    const EGY  = 'EGY';
    const ESH  = 'ESH';
    const ERI  = 'ERI';
    const ESP  = 'ESP';
    const ETH  = 'ETH';
    const FIN  = 'FIN';
    const FJI  = 'FJI';
    const FLK  = 'FLK';
    const FSM  = 'FSM';
    const FRO  = 'FRO';
    const FRA  = 'FRA';
    const GAB  = 'GAB';
    const GBR  = 'GBR';
    const GRD  = 'GRD';
    const GEO  = 'GEO';
    const GUF  = 'GUF';
    const GGY  = 'GGY';
    const GHA  = 'GHA';
    const GIB  = 'GIB';
    const GRL  = 'GRL';
    const GMB  = 'GMB';
    const GIN  = 'GIN';
    const GLP  = 'GLP';
    const GNQ  = 'GNQ';
    const GRC  = 'GRC';
    const SGS  = 'SGS';
    const GTM  = 'GTM';
    const GUM  = 'GUM';
    const GNB  = 'GNB';
    const GUY  = 'GUY';
    const HKG  = 'HKG';
    const HMD  = 'HMD';
    const HND  = 'HND';
    const HRV  = 'HRV';
    const HTI  = 'HTI';
    const HUN  = 'HUN';
    const IDN  = 'IDN';
    const IRL  = 'IRL';
    const ISR  = 'ISR';
    const IMN  = 'IMN';
    const IND  = 'IND';
    const IOT  = 'IOT';
    const IRQ  = 'IRQ';
    const IRN  = 'IRN';
    const ISL  = 'ISL';
    const ITA  = 'ITA';
    const JEY  = 'JEY';
    const JAM  = 'JAM';
    const JOR  = 'JOR';
    const JPN  = 'JPN';
    const KEN  = 'KEN';
    const KGZ  = 'KGZ';
    const KHM  = 'KHM';
    const KIR  = 'KIR';
    const COM  = 'COM';
    const KNA  = 'KNA';
    const PRK  = 'PRK';
    const KOR  = 'KOR';
    const KWT  = 'KWT';
    const CYM  = 'CYM';
    const KAZ  = 'KAZ';
    const LAO  = 'LAO';
    const LBN  = 'LBN';
    const LCA  = 'LCA';
    const LIE  = 'LIE';
    const LKA  = 'LKA';
    const LBR  = 'LBR';
    const LSO  = 'LSO';
    const LTU  = 'LTU';
    const LUX  = 'LUX';
    const LVA  = 'LVA';
    const LBY  = 'LBY';
    const MAR  = 'MAR';
    const MCO  = 'MCO';
    const MDA  = 'MDA';
    const MNE  = 'MNE';
    const MAF  = 'MAF';
    const MDG  = 'MDG';
    const MHL  = 'MHL';
    const MKD  = 'MKD';
    const MLI  = 'MLI';
    const MMR  = 'MMR';
    const MNG  = 'MNG';
    const MAC  = 'MAC';
    const MNP  = 'MNP';
    const MTQ  = 'MTQ';
    const MRT  = 'MRT';
    const MSR  = 'MSR';
    const MLT  = 'MLT';
    const MUS  = 'MUS';
    const MDV  = 'MDV';
    const MWI  = 'MWI';
    const MEX  = 'MEX';
    const MYS  = 'MYS';
    const MOZ  = 'MOZ';
    const NAM  = 'NAM';
    const NCL  = 'NCL';
    const NER  = 'NER';
    const NFK  = 'NFK';
    const NGA  = 'NGA';
    const NIC  = 'NIC';
    const NLD  = 'NLD';
    const NOR  = 'NOR';
    const NPL  = 'NPL';
    const NRU  = 'NRU';
    const NIU  = 'NIU';
    const NZL  = 'NZL';
    const OMN  = 'OMN';
    const PAN  = 'PAN';
    const PER  = 'PER';
    const PYF  = 'PYF';
    const PNG  = 'PNG';
    const PHL  = 'PHL';
    const PAK  = 'PAK';
    const POL  = 'POL';
    const SPM  = 'SPM';
    const PCN  = 'PCN';
    const PRI  = 'PRI';
    const PSE  = 'PSE';
    const PRT  = 'PRT';
    const PLW  = 'PLW';
    const PRY  = 'PRY';
    const QAT  = 'QAT';
    const REU  = 'REU';
    const ROU  = 'ROU';
    const ROM  = 'ROU';
    const SRB  = 'SRB';
    const RUS  = 'RUS';
    const RWA  = 'RWA';
    const SAU  = 'SAU';
    const SLB  = 'SLB';
    const SYC  = 'SYC';
    const SDN  = 'SDN';
    const SWE  = 'SWE';
    const SGP  = 'SGP';
    const SHN  = 'SHN';
    const SVN  = 'SVN';
    const SJM  = 'SJM';
    const SVK  = 'SVK';
    const SLE  = 'SLE';
    const SMR  = 'SMR';
    const SEN  = 'SEN';
    const SOM  = 'SOM';
    const SUR  = 'SUR';
    const SSD  = 'SSD';
    const STP  = 'STP';
    const SLV  = 'SLV';
    const SXM  = 'SXM';
    const SYR  = 'SYR';
    const SWZ  = 'SWZ';
    const TCA  = 'TCA';
    const TCD  = 'TCD';
    const ATF  = 'ATF';
    const TGO  = 'TGO';
    const THA  = 'THA';
    const TJK  = 'TJK';
    const TKL  = 'TKL';
    const TLS  = 'TLS';
    const TKM  = 'TKM';
    const TUN  = 'TUN';
    const TON  = 'TON';
    const TUR  = 'TUR';
    const TTO  = 'TTO';
    const TUV  = 'TUV';
    const TWN  = 'TWN';
    const TZA  = 'TZA';
    const UKR  = 'UKR';
    const UGA  = 'UGA';
    const UMI  = 'UMI';
    const USA  = 'USA';
    const URY  = 'URY';
    const UZB  = 'UZB';
    const VAT  = 'VAT';
    const VCT  = 'VCT';
    const VEN  = 'VEN';
    const VGB  = 'VGB';
    const VIR  = 'VIR';
    const VNM  = 'VNM';
    const VUT  = 'VUT';
    const WLF  = 'WLF';
    const WSM  = 'WSM';
    const YEM  = 'YEM';
    const MYT  = 'MYT';
    const ZAF  = 'ZAF';
    const ZMB  = 'ZMB';
    const ZWE  = 'ZWE';

    public function __construct(
        public readonly string $value
    ) {
    }

    public static function fromString(
        string $value
    ): self {
        return new self($value);
    }

    public function equals(ValueObject $object): bool
    {
        return $this->toPrimitive() === $object->toPrimitive() && $object instanceof self;
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->toPrimitive();
    }
}
