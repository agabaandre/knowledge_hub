import ElementNewsLetterMain from '@/components/elements/element-newsletter/ElementNewsLetterMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Newsletter - Education & Online Courses React NextJs Template",
};

const ElementNewsLetter = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ElementNewsLetterMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ElementNewsLetter;