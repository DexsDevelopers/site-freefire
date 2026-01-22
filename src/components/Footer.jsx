import React from 'react';
import { Link } from 'react-router-dom';
import { Facebook, Instagram, Twitter, MessageCircle } from 'lucide-react';

const Footer = () => {
  return (
    <footer className="bg-gray-900 border-t border-gray-800">
      <div className="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="col-span-1 md:col-span-1">
            <Link to="/" className="inline-block mb-4">
              <img 
                src="/logo-thunder.png" 
                alt="THUNDER STORE" 
                className="h-16 w-auto object-contain drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]"
              />
            </Link>
            <p className="mt-2 text-gray-400 text-sm">
              Sua loja confiável para produtos de Free Fire. Diamantes, contas e muito mais com entrega imediata.
            </p>
          </div>
          <div>
            <h3 className="text-sm font-semibold text-gray-300 tracking-wider uppercase">Produtos</h3>
            <ul className="mt-4 space-y-4">
              <li><Link to="/" className="text-base text-gray-500 hover:text-white">Diamantes</Link></li>
              <li><Link to="/" className="text-base text-gray-500 hover:text-white">Passes</Link></li>
              <li><Link to="/" className="text-base text-gray-500 hover:text-white">Contas</Link></li>
              <li><Link to="/" className="text-base text-gray-500 hover:text-white">Gift Cards</Link></li>
            </ul>
          </div>
          <div>
            <h3 className="text-sm font-semibold text-gray-300 tracking-wider uppercase">Suporte</h3>
            <ul className="mt-4 space-y-4">
              <li><Link to="/faq" className="text-base text-gray-500 hover:text-white">Como comprar</Link></li>
              <li><Link to="/termos" className="text-base text-gray-500 hover:text-white">Termos de Uso</Link></li>
              <li><Link to="/termos" className="text-base text-gray-500 hover:text-white">Política de Privacidade</Link></li>
              <li><a href="#" className="text-base text-gray-500 hover:text-white">Fale Conosco</a></li>
            </ul>
          </div>
          <div>
            <h3 className="text-sm font-semibold text-gray-300 tracking-wider uppercase">Redes Sociais</h3>
            <div className="flex space-x-6 mt-4">
              <a href="#" className="text-gray-400 hover:text-white transition-colors">
                <Instagram className="h-6 w-6" />
              </a>
              <a href="#" className="text-gray-400 hover:text-white transition-colors">
                <Facebook className="h-6 w-6" />
              </a>
              <a href="#" className="text-gray-400 hover:text-white transition-colors">
                <Twitter className="h-6 w-6" />
              </a>
              <a href="#" className="text-gray-400 hover:text-white transition-colors">
                <MessageCircle className="h-6 w-6" />
              </a>
            </div>
          </div>
        </div>
        <div className="mt-8 border-t border-gray-800 pt-8 md:flex md:items-center md:justify-between">
          <p className="text-base text-gray-400 text-center md:text-left">
            &copy; 2026 THUNDER STORE. Todos os direitos reservados.
          </p>
          <p className="text-base text-gray-500 text-center md:text-right mt-2 md:mt-0 text-sm">
            Desenvolvido para Gamers.
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
