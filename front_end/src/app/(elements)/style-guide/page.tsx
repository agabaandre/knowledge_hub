import StyleGuideMain from '@/components/elements/style-guide/StyleGuideMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Style Guide - Education & Online Courses React NextJs Template",
};

const StyleGuide = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <StyleGuideMain />
                </main>
            </Wrapper>
        </>
    );
};

export default StyleGuide;