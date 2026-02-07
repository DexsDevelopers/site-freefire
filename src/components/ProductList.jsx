import React from 'react';
import { products } from '../data/products';
import { Star } from 'lucide-react';

const ProductList = () => {
  return (
    <div className="bg-ff-black py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-black text-white uppercase tracking-wider">
            ESCOLHA O <span className="relative inline-block text-ff-red after:content-[''] after:absolute after:-bottom-2 after:left-0 after:w-full after:h-1 after:bg-ff-red">SEU JOGO</span>
          </h2>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          {products.map((product) => (
            <div key={product.id} className="bg-[#0f0f0f] rounded-2xl overflow-hidden border border-white/5 hover:border-ff-red/30 transition-all duration-300 group hover:-translate-y-2 shadow-2xl">
              <div className="relative h-56 overflow-hidden">
                <div className="absolute inset-0 bg-gradient-to-t from-[#0f0f0f] to-transparent z-10 opacity-60"></div>
                <img
                  className="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                  src={product.image}
                  alt={product.name}
                />
              </div>
              
              <div className="p-6 pt-2 text-center relative z-20">
                <h3 className="text-2xl font-black text-white uppercase tracking-wide mb-3">{product.name}</h3>
                
                <div className="flex items-center justify-center gap-1 mb-6">
                   {[...Array(5)].map((_, i) => (
                      <Star key={i} className="w-4 h-4 text-yellow-500 fill-current" />
                   ))}
                   <span className="text-gray-500 text-sm font-medium ml-1">({product.rating.toFixed(1)})</span>
                </div>

                <div className="inline-flex items-center gap-2 bg-[#1a1a1a] px-8 py-2 rounded-full border border-white/5 mb-6">
                   <div className="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_#22c55e]"></div>
                   <span className="text-gray-300 text-sm font-bold tracking-wide">{product.status}</span>
                </div>

                <button className="w-full bg-ff-red text-white font-black uppercase py-3.5 rounded-lg hover:bg-red-700 transition-colors tracking-wider shadow-[0_4px_14px_rgba(255,0,0,0.4)] hover:shadow-[0_6px_20px_rgba(255,0,0,0.6)]">
                  VER PLANOS
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default ProductList;
