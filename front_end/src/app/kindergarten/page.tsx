import KindergartenMain from '@/components/kindergarten/KindergartenMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Kindergarten - Education & Online Courses React NextJs Template",
};

const Kindergarten = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <KindergartenMain />
                </main>
            </Wrapper>
        </>
    );
};

export default Kindergarten;