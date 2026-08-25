import ElementCtaMain from '@/components/elements/element-cta/ElementCtaMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "CTA - Education & Online Courses React NextJs Template",
};

const ElementCta = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ElementCtaMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ElementCta;