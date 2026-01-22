import React from 'react';
import Hero from '../components/Hero';
import PromoSection from '../components/PromoSection';
import ProductList from '../components/ProductList';
import FeedbackSection from '../components/FeedbackSection';

const HomePage = () => {
  return (
    <>
      <Hero />
      <PromoSection />
      <ProductList />
      <FeedbackSection />
    </>
  );
};

export default HomePage;
