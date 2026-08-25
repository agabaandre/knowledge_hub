import ElementsBreadcrumb from '@/components/common/Breadcrumb/ElementsBreadcrumb';
import React from 'react';
import CtaStyleOne from './CtaStyleOne';
import CtaStyleTwo from './CtaStyleTwo';
import CtaStyleThree from './CtaStyleThree';
import CtaStyleFour from './CtaStyleFour';
import CtaStyleFive from './CtaStyleFive';
import CtaStyleSix from './CtaStyleSix';

const ElementCtaMain = () => {
    return (
        <>
            <ElementsBreadcrumb title='CTA' subTitle='CTA style' />
            <CtaStyleOne />
            <CtaStyleTwo />
            <CtaStyleThree />
            <CtaStyleFour />
            <CtaStyleFive/>
            <CtaStyleSix/>
        </>
    );
};

export default ElementCtaMain;