<?php
namespace App\Factories;

use App\Contracts\MailerFactoryInterface;
use Illuminate\Container\Container;
use Illuminate\Mail\Mailable;
use InvalidArgumentException;

final class MailerFactory implements MailerFactoryInterface
{
    /**
     * 
     * @param Container $container
     * @param array $map
     */
    public function __construct(
        protected Container $container,
        /** @var array<string, class-string<Mailable>> */
        protected array $map
        ) {}
        
        /**
         * 
         * @param string $key
         * @param array $parameters
         * @return Mailable
         */
        public function make(string $key, array $parameters = []): Mailable
        {
            $fqcn = $this->map[$key] ?? null;
            
            if (! $fqcn) {
                throw new InvalidArgumentException("Unknown mailable key [{$key}].");
            }
            
            /** @var Mailable $mailable */
            $mailable = $this->container->make($fqcn, $parameters);
            
            return $mailable;
        }
}

