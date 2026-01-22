import React from 'react';
import { Dices, Clock, AlertTriangle } from 'lucide-react';

const RoulettePage = () => {
  return (
    <div className="min-h-screen pt-24 pb-12 flex flex-col items-center justify-center relative overflow-hidden">
      {/* Background Effects */}
      <div className="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1605901309584-818e25960b8f?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-10"></div>
      <div className="absolute inset-0 bg-gradient-to-b from-black via-black/90 to-ff-black"></div>
      
      <div className="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <div className="mb-8 inline-block relative">
          <div className="absolute inset-0 bg-ff-red blur-[50px] opacity-20 animate-pulse"></div>
          <Dices className="w-32 h-32 text-ff-red mx-auto relative z-10 drop-shadow-[0_0_15px_rgba(255,0,0,0.5)]" />
        </div>
        
        <h1 className="text-6xl md:text-8xl font-black text-white uppercase tracking-tighter mb-4 text-glow">
          Roleta <span className="text-transparent bg-clip-text bg-gradient-to-r from-ff-red to-ff-orange">Diária</span>
        </h1>
        
        <div className="flex flex-col items-center justify-center gap-6 mt-12">
          <div className="bg-ff-gray/50 border border-white/10 p-8 rounded-2xl backdrop-blur-sm max-w-xl w-full">
            <div className="flex items-center justify-center gap-3 text-ff-yellow mb-4">
              <Clock className="w-8 h-8 animate-spin-slow" />
              <span className="text-2xl font-bold tracking-wider">EM BREVE</span>
            </div>
            
            <p className="text-gray-400 text-lg mb-8 leading-relaxed">
              Estamos preparando algo incrível para você! Em breve você poderá girar a roleta diariamente e ganhar prêmios exclusivos, diamantes e skins.
            </p>
            
            <div className="w-full bg-black/50 h-4 rounded-full overflow-hidden border border-white/5">
              <div className="h-full bg-gradient-to-r from-ff-red to-ff-orange w-[85%] relative overflow-hidden">
                <div className="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
              </div>
            </div>
            <div className="flex justify-between text-xs text-gray-500 mt-2 font-mono uppercase">
              <span>Desenvolvimento</span>
              <span>85%</span>
            </div>
          </div>
          
          <div className="flex items-center gap-2 text-gray-500 text-sm mt-8 bg-black/40 px-4 py-2 rounded-full border border-white/5">
            <AlertTriangle className="w-4 h-4 text-ff-red" />
            <span>Notifique-me quando lançar em nosso Discord</span>
          </div>
        </div>
      </div>
    </div>
  );
};

export default RoulettePage;
