import AboutKindergartenMain from '@/components/pages/page-layout-one/about-kindergarten/AboutKindergartenMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "About Kindergarten - Education & Online Courses React NextJs Template",
};

const AboutKindergarten = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <AboutKindergartenMain />
                </main>
            </Wrapper>
        </>
    );
};

export default AboutKindergarten;