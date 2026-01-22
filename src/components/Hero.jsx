import React from 'react';
import { Link } from 'react-router-dom';
import { Eye } from 'lucide-react';

const Hero = () => {
  return (
    <div className="relative min-h-screen bg-ff-black flex items-center overflow-hidden">
      {/* Background Effect */}
      <div className="absolute inset-0 z-0">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-gray-800/20 via-ff-black to-ff-black opacity-50"></div>
        <div className="absolute top-0 left-0 w-full h-full opacity-10" 
             style={{
               backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`
             }}>
        </div>
        {/* White/Gray glow spots for Thunder theme */}
        <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-[128px]"></div>
      </div>

      <div className="relative z-10 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col items-center text-center justify-center min-h-screen pt-20 pb-10">
        {/* Content */}
        <div className="space-y-8 flex flex-col items-center max-w-4xl mx-auto">
          {/* Image as Title */}
          <div className="relative flex justify-center w-full">
             <img 
               src="/logo-thunder.png" 
               alt="THUNDER STORE" 
               className="w-full max-w-4xl drop-shadow-[0_0_30px_rgba(255,255,255,0.15)] transform hover:scale-105 transition-transform duration-500"
               onError={(e) => {
                 e.target.style.display = 'none';
                 e.target.nextSibling.style.display = 'block';
               }}
             />
             {/* Fallback Text if image not found */}
             <h1 className="text-6xl md:text-8xl font-black tracking-tighter leading-none hidden">
                <span className="block text-white">THUNDER</span>
                <span className="block text-gray-400 text-glow">STORE</span>
             </h1>
          </div>
          
          <p className="text-gray-400 text-lg md:text-2xl max-w-2xl font-medium leading-relaxed">
            A melhor loja de Free Fire do cenário.
            <br />
            Produtos exclusivos, entrega rápida e segurança total.
          </p>

          <div className="flex flex-col sm:flex-row gap-6 justify-center w-full">
            <button className="bg-white text-black hover:bg-gray-200 font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1 shadow-lg">
              <Eye className="w-5 h-5" /> Ver Produtos
            </button>
            <Link to="/painel" className="bg-ff-red hover:bg-ff-dark-red text-white font-bold py-4 px-10 rounded-full flex items-center justify-center gap-2 shadow-[0_0_20px_rgba(255,0,51,0.4)] hover:shadow-[0_0_30px_rgba(255,0,51,0.6)] transition-all uppercase tracking-wide text-sm sm:text-base hover:-translate-y-1">
              Acessar Painel
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Hero;
