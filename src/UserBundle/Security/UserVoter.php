<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 13/03/2017
 * Time: 13:06
 */

namespace UserBundle\Security;


use AppBundle\Entity\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use UserBundle\Entity\User;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = "edit";

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT))){
            return false;
        }

        if(!$subject instanceof Ticket) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        foreach($token->getRoles() as $role) {
            if (in_array($role->getRole(), ['ROLE_ADMIN'])){
                return true;
            }
        }

        $ticket = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($ticket, $user);
            case self::EDIT:
                return $this->canEdit($ticket, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Ticket $ticket, User $user)
    {
        if ($this->canEdit($ticket, $user)){
            return true;
        }
    }

    private function canEdit(Ticket $ticket, User $user)
    {
        return $user === $ticket->getFromWho();
    }
}
