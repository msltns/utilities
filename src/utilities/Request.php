<?php

namespace msltns\utilities;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class represents an implementation of Psr\Http\Message\RequestInterface.
 *
 * @category 	Class
 * @package  	Utilities
 * @author 		msltns <info@msltns.com>
 * @version  	0.0.1
 * @since   	0.0.1
 * @license 	GPL 3
 *          	This program is free software; you can redistribute it and/or modify
 *          	it under the terms of the GNU General Public License, version 3, as
 *          	published by the Free Software Foundation.
 *          	This program is distributed in the hope that it will be useful,
 *          	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *          	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *          	GNU General Public License for more details.
 *          	You should have received a copy of the GNU General Public License
 *          	along with this program; if not, write to the Free Software
 *          	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! class_exists( '\msltns\utilities\Request' ) ) {
    
    class Request implements ServerRequestInterface {
        
        private $request;
        
        public function __construct( ServerRequestInterface $request ) {
            $this->request = $request;
        }
        
        /*************************************** 
         * MessageInterface 
         ***************************************/
        
        public function getProtocolVersion(): string { return $this->request->getProtocolVersion(); }

        public function withProtocolVersion( string $version ): MessageInterface { return $this->request->withProtocolVersion( $version ); }

        public function getHeaders(): array { return $this->request->getHeaders(); }

        public function hasHeader( string $name ): bool { return $this->request->hasHeader( $name ); }

        public function getHeader( string $name ): array { return $this->request->getHeader( $name ); }

        public function getHeaderLine( string $name ): string { return $this->request->getHeaderLine( $name ); }

        public function withHeader( string $name, $value ): MessageInterface { return $this->request->withHeader( $name, $value ); }

        public function withAddedHeader( string $name, $value ): MessageInterface { return $this->request->withAddedHeader( $name, $value ); }

        public function withoutHeader( string $name ): MessageInterface { return $this->request->withoutHeader( $name ); }

        public function getBody(): StreamInterface { return $this->request->getBody(); }

        public function withBody( StreamInterface $body ): MessageInterface { return $this->request->withBody( $body ); }
        
        /*************************************** 
         * RequestInterface 
         ***************************************/
        
        public function getRequestTarget(): string { return $this->request->getRequestTarget(); }

        public function withRequestTarget( string $requestTarget ): RequestInterface { return $this->request->withRequestTarget( $requestTarget ); }

        public function getMethod(): string { return $this->request->getMethod(); }

        public function withMethod( string $method ): RequestInterface { return $this->request->withMethod( $method ); }

        public function getUri(): UriInterface { return $this->request->getUri(); }

        public function withUri( UriInterface $uri, bool $preserveHost = false ): RequestInterface { return $this->request->withUri( $uri, $preserveHost ); }
        
        /*************************************** 
         * ServerRequestInterface 
         ***************************************/
        
        public function getServerParams(): array { return $this->request->getServerParams(); }

        public function getCookieParams(): array { return $this->request->getCookieParams(); }

        public function withCookieParams( array $cookies ): ServerRequestInterface { return $this->request->withCookieParams( $cookies ); }

        public function getQueryParams(): array { return $this->request->getQueryParams(); }

        public function withQueryParams( array $query ): ServerRequestInterface { return $this->request->withQueryParams( $query ); }

        public function getUploadedFiles(): array { return $this->request->getUploadedFiles(); }

        public function withUploadedFiles( array $uploadedFiles ): ServerRequestInterface { return $this->request->withUploadedFiles( $uploadedFiles ); }

        public function getParsedBody() { return $this->request->getParsedBody(); }

        public function withParsedBody( $data ): ServerRequestInterface { return $this->request->withParsedBody( $data ); }

        public function getAttributes(): array { return $this->request->getAttributes(); }

        public function getAttribute( string $name, $default = null ) { return $this->request->getAttribute( $name, $default ); }

        public function withAttribute( string $name, $value ): ServerRequestInterface { return $this->request->withAttribute( $name, $value ); }

        public function withoutAttribute( string $name ): ServerRequestInterface { return $this->request->withoutAttribute( $name ); }
        
        /*************************************** 
         * Own implementation 
         ***************************************/
        
        public function getParam( string $name, mixed $default = null ) {
            
            $get_params = (array) $this->request->getQueryParams();
            if ( is_array( $get_params ) && !empty( $get_params ) && isset( $get_params[$name] ) ) {
                return $get_params[$name];
            }
            
            $post_params = (array) $this->request->getParsedBody();
            if ( is_array( $post_params ) && !empty( $post_params ) && isset( $post_params[$name] ) ) {
                return $post_params[$name];
            }
            
            return $default;
        }
        
    }
    
}
