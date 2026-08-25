import ElementProgressBarMain from '@/components/elements/element-progress-bar/ElementProgressBarMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Progress Bar - Education & Online Courses React NextJs Template",
};

const Progressbar = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ElementProgressBarMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Progressbar;