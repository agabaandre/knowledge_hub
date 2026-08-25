import ModernSchoolingMain from '@/components/modern-schooling/ModernSchoolingMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Modern Shooling - Education & Online Courses React NextJs Template",
};

const ModernSchooling = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <ModernSchoolingMain />
                </main>
            </Wrapper>
        </>
    );
};

export default ModernSchooling;