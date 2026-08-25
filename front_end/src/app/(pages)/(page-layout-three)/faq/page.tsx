import FaqMain from '@/components/pages/page-layout-three/faq/FaqMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Faq - Education & Online Courses React NextJs Template",
};

const Faq = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <FaqMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Faq;