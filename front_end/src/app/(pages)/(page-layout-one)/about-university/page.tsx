import AboutUniversityMain from '@/components/pages/page-layout-one/about-university/AboutUniversityMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "About University - Education & Online Courses React NextJs Template",
};

const AboutUniversity = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <AboutUniversityMain />
                </main>
            </Wrapper>
        </>
    );
};

export default AboutUniversity;