<?php

/**
 * ttNieuwsbrief class
 *
 */
class ttNieuwsbriefActions extends sfActions
{

  public function preExecute()
  { 
    $this->veld = sfConfig::get('sf_ttNieuwsbrief_veld', 'actief');
    $this->setter = 'set' . sfInflector::camelize($this->veld);    
  }

  /**
   * Haal het EmailAdres of Persoon Object op dat gekoppeld is aan de uuid
   */
  public function getEmailObjectByUuid($uuid)
  {
    $obj = EmailAdresPeer::retrieveByUuid($uuid);
    
    if (! $obj)
    {
      $obj =  PersoonPeer::retrieveByUuid($uuid);
    }
    
    return $obj;
  }


  /**
   * Haal het EmailAdres of Persoon Object op dat gekoppeld is aan het emailadres
   */
  public function getEmailObjectByEmail($email)
  {
    $obj = EmailAdresPeer::retrieveByEmail($email);
    
    if (! $obj)
    {
      $obj =  PersoonPeer::retrieveByEmail($email);
    }
    
    return $obj;
  }


	/**
	 *
	 */	
	public function executeInschrijven()
	{
    $this->interesse_gebieden = InteresseGebiedPeer::getGebieden();
    	 
    $validator = new sfEmailValidator();
    $validator->initialize(sfContext::getInstance());
    $error = null;
    $email = $this->getRequestParameter('email');
            
    if (! $validator->execute($email, $error))
    {
      $this->ongeldig_adres = true;
      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  
    $bestaand = $this->getEmailObjectByEmail(urldecode($this->getRequestParameter('email')));
    
    if ($bestaand)
    {
      $this->reeds_lid = true;
      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  
    $emailAdres = new EmailAdres();
    $emailAdres->setEmail(urldecode($this->getRequestParameter('email')));
    $emailAdres->setVoornaam($this->getRequestParameter('voornaam'));
    $emailAdres->setAchternaam($this->getRequestParameter('achternaam'));
    $emailAdres->setActief(false);
    
    try 
    {
      $emailAdres->save();
    }
    catch(PropelException $e)
    {
      $this->reeds_lid = true;
      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
    
    foreach($this->interesse_gebieden as $interesse) 
    {
      InteresseGebiedPeer::setGeinteresseerdIn($interesse, $this->hasRequestParameter('interesse_' . $interesse->getId()), $emailAdres->getId(),'EmailAdres');
    }

    try
    {
      $this->sendNewAccountMail($emailAdres);
      $this->mededeling = 'bevestigen';
    }
    catch(exception $e)
    {
      $this->mededeling = 'error';
      $this->error = $e->getMessage();
    }

    $this->setTemplate('mededeling');
  }
  
	/**
	 *
	 */	  
	public function executeActiveer()
	{
    $emailAdres = $this->getEmailObjectByUuid($this->getRequestParameter('uuid'));
    
    if ($emailAdres && ($emailAdres->getEmail() == $this->getRequestParameter('email')))
    {
      $emailAdres->setActief(true);
      $emailAdres->save();
      $this->setTemplate('mededeling');
      $this->mededeling = 'bevestigd';
    }
    else 
    {
      $this->forward404();
    }
  }
  
 
	/**
	 *
	 */	  
	public function executeBewerk()
	{
	 
    $emailAdres = $this->getEmailObjectByUuid($this->getRequestParameter('uuid'));
    $this->interesse_gebieden = InteresseGebiedPeer::getGebieden();
    
    if ($emailAdres && ($emailAdres->getEmail() == urldecode($this->getRequestParameter('email'))))
    {
      $this->email_adres = $emailAdres;
      $this->persoonlijke_interesses = InteresseGebiedPeer::getInteresseGebieden($this->email_adres->getId(), get_class($emailAdres));
    }
    else 
    {
      $this->forward404();
    }

  }
  
	/**
	 *
	 */	  
	public function executeUpdate()
	{
    $this->interesse_gebieden = InteresseGebiedPeer::getGebieden();
    	 
    $emailAdres = $this->getEmailObjectByUuid($this->getRequestParameter('uuid'));
    
    if ($emailAdres && ($emailAdres->getEmail() == urldecode($this->getRequestParameter('email'))))
    {
      // Enkel EmailAdres kan z'n voor- en achternaam wijzigen, een Persoon niet.
      if ($emailAdres instanceOf EmailAdres)
      {
        $emailAdres->setVoornaam($this->getRequestParameter('voornaam'));
        $emailAdres->setAchternaam($this->getRequestParameter('achternaam'));
        // Altijd terug op actief zetten mocht iemand (opnieuw) interesses aanvinken
        $emailAdres->setActief(true);
        $emailAdres->save();
      }

      // Sla interesses op
      foreach($this->interesse_gebieden as $interesse)
      {
        InteresseGebiedPeer::setGeinteresseerdIn($interesse, $this->hasRequestParameter('interesse_' . $interesse->getId()), $emailAdres->getId(), get_class($emailAdres));
      }
      
      $this->email_adres = $emailAdres;
      $this->persoonlijke_interesses = InteresseGebiedPeer::getInteresseGebieden($emailAdres->getId(), get_class($emailAdres));
      
      $this->opgeslagen = true;
      
      $this->setTemplate('bewerk');
    }
    else 
    {
      $this->forward404();
    }  
  }
  
	/**
	 *
	 */	  
	public function executeUitschrijven()
	{
    $this->interesse_gebieden = InteresseGebiedPeer::getGebieden();
    $emailAdres = $this->getEmailObjectByUuid($this->getRequestParameter('uuid'));

    if ($emailAdres && ($emailAdres->getEmail() == urldecode($this->getRequestParameter('email'))))
    {
      // Verwijder alle interesses
      // Sla interesses op
      foreach($this->interesse_gebieden as $interesse)
      {
        InteresseGebiedPeer::setGeinteresseerdIn($interesse, false, $emailAdres->getId(),'EmailAdres');
      }
      
      if ($emailAdres instanceof EmailAdres)
      {
        $emailAdres->setActief(false);
      }
      else
      {
        // ophalen uit config van te deactiveren veld
        if (!method_exists($emailAdres, $this->setter))
        {
          throw new Exception (eval("return " . get_class($emailAdres) . "Peer::TABLE_NAME;") . "." . $this->veld . " bestaat niet.");
        }
        $emailAdres->{$this->setter}(false);
      }
      
      $emailAdres->save();
      $this->setTemplate('mededeling');
      $this->mededeling = 'uitgeschreven';
    }
    else 
    {
      $this->forward404();
    }
      
  }
  
	/**
	 *
	 */	  
	public function executeSendLink()
	{
    $emailAdres = urldecode($this->getRequestParameter('email')) ? $this->getEmailObjectByEmail(urldecode($this->getRequestParameter('email'))) : null;
    
    // om automatisch onmiddellijk te kunnen uitschrijven    
    $direct = $this->getRequestParameter('direct', 0);
    
    if ($emailAdres)
    {
      try
      {
        $this->sendAccountInfoMail($emailAdres, $direct);
        $this->mededeling = 'linkverzonden';
      }
      catch(exception $e)
      {
        $this->mededeling = 'error';
        $this->error = $e->getMessage();
      }
    }
    else
    {
      $this->mededeling = 'emailnietgevonden';
      $this->email = $this->getRequestParameter('email');
    }
    
    $this->setTemplate('mededeling');
  }
 
	/**
	 *
	 */	  
	public function executeIndex()
	{
    $this->interesse_gebieden = InteresseGebiedPeer::getGebieden();
 	}
 	



  private function sendNewAccountMail($emailAdres)
  {
    Misc::use_helper('Url');
    $naam = $emailAdres->getNaam();
    if ($naam) 
    {
      $naam = ' ' . $naam;
    } 
    
    $urlprefixBevestig = url_for('ttNieuwsbrief/activeer', true);
    $urlprefixBewerk = url_for('ttNieuwsbrief/bewerk', true);
    
    $inhoud = "
    
Beste{$naam},

Om uw inschrijving op onze mailinglijst te bevestigen, gelieve op volgende link te klikken of deze te kopiëren en in de adresbalk van uw webbrowser te plakken:

{$urlprefixBevestig}/uuid/{$emailAdres->getUuid()}/email/" . urlencode($emailAdres->getEmail()) . "

Pas nadat u bevestigd heeft, zal u email van ons ontvangen.

Als u later uw instelling wenst te wijzigen, dan kan dat via volgende link:

{$urlprefixBewerk}/uuid/{$emailAdres->getUuid()}/email/" . urlencode($emailAdres->getEmail()) . "

Met vriendelijke groeten,
  " . sfConfig::get("sf_mail_sender_name");
    ;
    
    BerichtPeer::verstuurEmail($emailAdres->getEmail(), nl2br($inhoud), array('skip_template' => true, 'onderwerp' => 'Inschrijving bevestigen'));

  }


  private function sendAccountInfoMail($emailAdres, $direct = 0)
  {
    Misc::use_helper('Url');
    
    $naam = $emailAdres->getNaam();
    $urlprefix = url_for('ttNieuwsbrief/' . ($direct ? 'uitschrijven' : 'bewerk'), true);
    $inhoud = "
    
Beste {$naam},

In deze e-mail vindt u de link die u aanvroeg via onze website.
Om de instellingen van uw inschrijving op onze nieuwsbrieven aan te passen of om u uit te schrijven,
klik op volgende link of kopiëer deze en plak ze in de adresbalk van uw webbrowser.

{$urlprefix}/uuid/{$emailAdres->getUuid()}/email/" . urlencode($emailAdres->getEmail()) . "

Met vriendelijke groeten,
      " . sfConfig::get("sf_mail_sender_name");

    BerichtPeer::verstuurEmail($emailAdres->getEmail(), nl2br($inhoud), array('skip_template' => true, 'onderwerp' => 'Link om uw account te beheren'));
   
    
  }
}
