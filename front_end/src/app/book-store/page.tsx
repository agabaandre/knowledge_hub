import BookStoreMain from '@/components/book-store/BookStoreMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Book Store - Education & Online Courses React NextJs Template",
};

const BookStore = () => {
    return (
        <>
            <Wrapper>
                <main className="main-area">
                    <BookStoreMain />
                </main>
            </Wrapper>
        </>
    );
};

export default BookStore;